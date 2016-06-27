<?php
/**
 * Line Implementation
 */

namespace Webbing\Nomadlog\Location;

use Webbing\Nomadlog\Location\Bearing\BearingInterface;
use Webbing\Nomadlog\Location\Distance\DistanceInterface;
use Webbing\Nomadlog\Location\Distance\Vincenty;

/**
 * Line Implementation
 */
class Line implements GeometryInterface
{
    /**
     * @var \Webbing\Nomadlog\Location\Coordinate
     */
    protected $point1;

    /**
     * @var \Webbing\Nomadlog\Location\Coordinate
     */
    protected $point2;
    protected $precision = 5;
    protected $distanceCalculator;

    /**
     * @param Coordinate $point1
     * @param Coordinate $point2
     */
    public function __construct(Coordinate $point1, Coordinate $point2)
    {
        $this->point1 = $point1;
        $this->point2 = $point2;
        $this->distanceCalculator = new Vincenty();
    }

    /**
     * @param \Webbing\Nomadlog\Location\Coordinate $point1
     */
    public function setPoint1($point1)
    {
        $this->point1 = $point1;
    }

    /**
     * @return \Webbing\Nomadlog\Location\Coordinate
     */
    public function getPoint1()
    {
        return $this->point1;
    }

    /**
     * @param \Webbing\Nomadlog\Location\Coordinate $point2
     */
    public function setPoint2($point2)
    {
        $this->point2 = $point2;
    }

    /**
     * @return \Webbing\Nomadlog\Location\Coordinate
     */
    public function getPoint2()
    {
        return $this->point2;
    }

    /**
     * Returns an array containing the two points.
     *
     * @return array
     */
    public function getPoints()
    {
        return [$this->point1, $this->point2];
    }

    /**
     * Returns the half-way point / coordinate along a great circle
     * path between the origin and the destination coordinates.
     *
     * @return \Webbing\Nomadlog\Location\Coordinate
     */
    public function middle()
    {
        return $this->middlePoint($this->point1, $this->point2);
    }

    /**
     * @param Coordinate $a
     * @param Coordinate $b
     * @return Coordinate
     */
    protected function middlePoint($a, $b)
    {
        $latA = deg2rad($a->getLat());
        $lngA = deg2rad($a->getLng());
        $latB = deg2rad($b->getLat());
        $lngB = deg2rad($b->getLng());
        $bx = cos($latB) * cos($lngB - $lngA);
        $by = cos($latB) * sin($lngB - $lngA);
        $lat3 = rad2deg(atan2(sin($latA) + sin($latB), sqrt((cos($latA) + $bx) * (cos($latA) + $bx) + $by * $by)));
        $lng3 = rad2deg($lngA + atan2($by, cos($latA) + $bx));
        return new Coordinate($lat3, $lng3);
    }


    /**
     * @param Coordinate $point
     * @return Coordinate
     */
    public function getNearestPoint(Coordinate $point)
    {
        /**
         * We can use two approach to determine nearest segment.
         * Using great circle intersections to determine perpendicular distance (sometimes is not very accurate)
         * Or using a "binary search" like approach to determine line segment nearest middle point.
         */
        return $this->nearestPointMiddle($this->getPoint1(), $this->getPoint2(), $point);
    }

    /**
     * Get nearest segment using great circle calculation.
     * sometimes is not very accurate
     *
     * @param Coordinate $a
     * @param Coordinate $b
     * @param Coordinate $c
     * @return Coordinate
     */
    protected function nearestPointSegment ($a, $b, $c)
    {
        $t = $this->nearestPointGreatCircle($a,$b,$c);
        if ($this->onSegment($a,$b,$t))
            return $t;
        return $this->middlePoint($a, $b);
    }

    /**
     * Get nearest point in segment using a "binary search" approach
     * Calculate middle point (M) of A-B segment, then determine distance between C and A-M and B-M
     * So, interacts with nearest segment (A-M or B-M) recursively while the distance between the
     * middle point (M) and B (or A, whatever) is greater than 1
     *
     * @param $a
     * @param $b
     * @param $c
     * @param int $maxInteractions
     * @return Coordinate
     */
    protected function nearestPointMiddle($a, $b, $c, $maxInteractions = 1){
        $middle = $this->middlePoint($a, $b);
        $aD = $this->onSegment($a, $middle, $c, false);
        $bD = $this->onSegment($b, $middle, $c, false);

        $middle->setPropertie('segmentPrecision', $maxInteractions);
        if($bD < $aD){
            $a = $b;
        }

        return $middle->getDistance($b, $this->distanceCalculator) > 1 ? $this->nearestPointMiddle($a, $middle, $c, $maxInteractions + 1) : $middle;
    }

    /**
     * @param Coordinate $a
     * @param Coordinate $b
     * @param Coordinate $c
     * @return Coordinate
     */
    protected function nearestPointGreatCircle($a, $b, $c)
    {
        $a_ = Cartesian::fromLatLong($a);
        $b_ = Cartesian::fromLatLong($b);
        $c_ = Cartesian::fromLatLong($c);

        $g = $a_->vectorProduct($b_)->normalize()->multiplyByScalar();
        $f = $c_->vectorProduct($g)->normalize()->multiplyByScalar();
        $t = $g->vectorProduct($f)->normalize()->multiplyByScalar();

        if(isset($_GET['debug'])) {
            echo "Cartesian <br>";
            echo "a: " . $a_ . "<br>";
            echo "b: " . $b_ . "<br>";
            echo "c: " . $c_ . "<br>";
            echo "g: " . $g . "<br>";
            echo "f: " . $f . "<br>";
            echo "t: " . $t . "<br><br>";
            echo "Lat/Lng <br>";
            echo "a: " . $a . " -from cartesian: " . $a_->toLatitudeLongitude() . "<br>";
            echo "b: " . $b . " -from cartesian: " . $b_->toLatitudeLongitude() . "<br>";
            echo "c: " . $c . " -from cartesian: " . $c_->toLatitudeLongitude() . "<br>";
            echo "g: " . $g->toLatitudeLongitude() . "<br>";
            echo "f: " . $f->toLatitudeLongitude() . "<br>";
            echo "t: " . $t->toLatitudeLongitude() . "<br>";
            echo "distance t -> c: " . $t->toLatitudeLongitude()->getDistance($c, $this->distanceCalculator) . "<br>";
        }

        return $t->toLatitudeLongitude();
    }

    /**
     * @param Coordinate $a
     * @param Coordinate $b
     * @param Coordinate $t
     * @return Coordinate
     */
    protected function onSegment ($a, $b, $t, $returnBool = true)
    {
        // should be   return distance(a,t)+distance(b,t)==distance(a,b),
        // but due to rounding errors, we use:
        $d = abs($a->getDistance($b,$this->distanceCalculator)-$a->getDistance($t, $this->distanceCalculator)-$b->getDistance($t, $this->distanceCalculator));
        if(isset($_GET['debug'])) {
            echo "onSegment: ".$d."<br>";
        }
        if($returnBool)
            $t->setPropertie('segmentPrecision', $d);
        return $returnBool ? ($d < $this->precision) : $d;
    }

    /**
     * Calculates the length of the line (distance between the two
     * coordinates).
     *
     * @param DistanceInterface $calculator instance of distance calculation class
     *
     * @return float
     */
    public function getLength(DistanceInterface $calculator)
    {
        return $calculator->getDistance($this->point1, $this->point2);
    }

    /**
     * Get distance between this line and a point
     * @param Coordinate $point
     * @return float
     */
    public function getDistance(Coordinate $point)
    {
        return $this->getNearestPoint($point)->getDistance($point, $this->distanceCalculator);
    }

    /**
     * @param \Webbing\Nomadlog\Location\Bearing\BearingInterface $bearingCalculator
     *
     * @return float
     */
    public function getBearing(BearingInterface $bearingCalculator)
    {
        return $bearingCalculator->calculateBearing($this->point1, $this->point2);
    }

    /**
     * Create a new instance with reversed point order, i. e. reversed direction.
     *
     * @return Line
     */
    public function getReverse()
    {
        return new static($this->point2, $this->point1);
    }
}
