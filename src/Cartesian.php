<?php
/**
 * Coordinate Implementation
 */

namespace Webbing\Nomadlog\Location;

/**
 * ECEF Cartesian coordinate
 */
class Cartesian
{
    /**
     * X
     * @var float
     */
    protected $x;

    /**
     * Y
     * @var float
     */
    protected $y;

    /**
     * Z
     * @var float
     */
    protected $z;

    /**
     * Reference ellipsoid used in this datum
     * @var Ellipsoid
     */
    protected $ellipsoid;

    /**
     * Cartesian constructor.
     * @param float $x
     * @param float $y
     * @param float $z
     * @param Ellipsoid $ellipsoid
     */
    public function __construct($x, $y, $z, Ellipsoid $ellipsoid = null)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setZ($z);

        if ($ellipsoid !== null) {
            $this->setEllipsoid($ellipsoid);
        } else {
            $this->setEllipsoid(Ellipsoid::createDefault());
        }
    }

    /**
     * String version of coordinate.
     * @return string
     */
    public function __toString()
    {
        return "({$this->x}, {$this->y}, {$this->z})";
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param float $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param float $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return float
     */
    public function getZ()
    {
        return $this->z;
    }

    /**
     * @param float $z
     */
    public function setZ($z)
    {
        $this->z = $z;
    }

    /**
     * @return Ellipsoid
     */
    public function getEllipsoid()
    {
        return $this->ellipsoid;
    }

    /**
     * @param Ellipsoid $ellipsoid
     */
    public function setEllipsoid($ellipsoid)
    {
        $this->ellipsoid = $ellipsoid;
    }

    /**
     * Convert these coordinates into a latitude, longitude
     * Formula for transformation is taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     *
     * @return Coordinate
     */
    public function toLatitudeLongitude()
    {
        $lambda = rad2deg(atan2($this->y, $this->x));
        $p = sqrt(pow($this->x, 2) + pow($this->y, 2));
        $phi = atan($this->z / ($p * (1 - $this->ellipsoid->getEcc())));
        do {
            $phi1 = $phi;
            $v = $this->ellipsoid->getMaj() / (sqrt(1 - $this->ellipsoid->getEcc() * pow(sin($phi), 2)));
            $phi = atan(($this->z + ($this->ellipsoid->getEcc() * $v * sin($phi))) / $p);
        } while (abs($phi - $phi1) >= 0.00001);
        $h = $p / cos($phi) - $v;
        $phi = rad2deg($phi);
        return new Coordinate($phi, $lambda, $h, $this->ellipsoid);
    }

    /**
     * Convert a latitude, longitude height to x, y, z
     * Formula for transformation is taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     *
     * @param Coordinate $latLng
     * @return Cartesian
     */
    public static function fromLatLong(Coordinate $latLng)
    {
        $a = $latLng->getEllipsoid()->getMaj();
        $eSquared = $latLng->getEllipsoid()->getEcc();
        $phi = deg2rad($latLng->getLat());
        $lambda = deg2rad($latLng->getLng());
        $v = $a / (sqrt(1 - $eSquared * pow(sin($phi), 2)));
        $x = ($v + $latLng->getH()) * cos($phi) * cos($lambda);
        $y = ($v + $latLng->getH()) * cos($phi) * sin($lambda);
        $z = ((1 - $eSquared) * $v + $latLng->getH()) * sin($phi);
        return new static($x, $y, $z, $latLng->getEllipsoid());
    }

    /**
     * Calculate vector product from this cartesian to $b
     * @param Cartesian $b
     * @return Cartesian
     */
    public function vectorProduct(Cartesian $b)
    {

        $x = ($this->getY() * $b->getZ()) - ($this->getZ() * $b->getY());
        $y = ($this->getZ() * $b->getX()) - ($this->getX() * $b->getZ());
        $z = ($this->getX() * $b->getY()) - ($this->getY() * $b->getX());

        return new Cartesian($x, $y, $z);
    }

    /**
     * Normalize
     * @return $this
     */
    public function normalize()
    {
        $length = sqrt(pow($this->x, 2) + pow($this->y, 2) + pow($this->z, 2));
        $this->x /= $length;
        $this->y /= $length;
        $this->z /= $length;
        return $this;
    }

    /**
     * Multiply by scalar
     * @return $this
     */
    public function multiplyByScalar()
    {
        $this->x = $this->x * $this->getEllipsoid()->getArithmeticMeanRadius();
        $this->y = $this->y * $this->getEllipsoid()->getArithmeticMeanRadius();
        $this->z = $this->z * $this->getEllipsoid()->getArithmeticMeanRadius();
        return $this;
    }

    /**
     * Transform the datum used for these coordinates by using a Helmert Transform
     * @param Ellipsoid $toEllipsoid
     * @param float $tranX
     * @param float $tranY
     * @param float $tranZ
     * @param float $scale
     * @param float $rotX rotation about x-axis in radians
     * @param float $rotY rotation about y-axis in radians
     * @param float $rotZ rotation about z-axis in radians
     * @return mixed
     */
    public function transformDatum(Ellipsoid $toEllipsoid, $tranX, $tranY, $tranZ, $scale, $rotX, $rotY, $rotZ)
    {
        $x = $tranX + ($this->getX() * (1 + $scale)) - ($this->getY() * $rotZ) + ($this->getZ() * $rotY);
        $y = $tranY + ($this->getX() * $rotZ) + ($this->getY() * (1 + $scale)) - ($this->getZ() * $rotX);
        $z = $tranZ - ($this->getX() * $rotY) + ($this->getY() * $rotX) + ($this->getZ() * (1 + $scale));
        $this->setX($x);
        $this->setY($y);
        $this->setZ($z);
        $this->setEllipsoid($toEllipsoid);
    }
}
