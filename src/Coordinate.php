<?php
/**
 * Coordinate Implementation
 */

namespace Webbing\Nomadlog\Location;

use Webbing\Nomadlog\Location\Distance\DistanceInterface;
use Webbing\Nomadlog\Location\Formatter\Coordinate\FormatterInterface;

/**
 * Coordinate Implementation
 */
class Coordinate implements GeometryInterface
{
    /**
     * @var float
     */
    protected $lat;

    /**
     * @var float
     */
    protected $lng;

    /**
     * Height
     * @var float
     */
    protected $h;

    /**
     * @var Ellipsoid
     */
    protected $ellipsoid;

    /**
     * Custom properties
     * @var array
     */
    protected $properties = array();

    /**
     * @param float $lat           -90.0 .. +90.0
     * @param float $lng           -180.0 .. +180.0
     * @param float $height
     * @param Ellipsoid $ellipsoid if omitted, WGS-84 is used
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($lat, $lng, $height = 0, Ellipsoid $ellipsoid = null)
    {
        if (! $this->isValidLatitude($lat)) {
            throw new \InvalidArgumentException("Latitude value must be numeric -90.0 .. +90.0 (given: {$lat})");
        }

        if (! $this->isValidLongitude($lng)) {
            throw new \InvalidArgumentException("Longitude value must be numeric -180.0 .. +180.0 (given: {$lng})");
        }

        $this->lat = doubleval($lat);
        $this->lng = doubleval($lng);
        $this->h = round($height);

        if ($ellipsoid !== null) {
            $this->ellipsoid = $ellipsoid;
        } else {
            $this->ellipsoid = Ellipsoid::createDefault();
        }
    }

    /**
     * Set custom propertie
     * @param $key
     * @param null $value
     */
    public function setPropertie($key, $value = null){
        if(!empty($key)){
            $this->properties[$key] = $value;
        }
    }

    /**
     * Get custom propertie
     * @param $key
     * @return mixed
     */
    public function getPropertie($key){
        return isset($this->properties[$key]) ? $this->properties[$key] : null;
    }

    /**
     * Check if custom propertie is set
     * @param $key
     * @return mixed
     */
    public function hasPropertie($key){
        return isset($this->properties[$key]);
    }

    /**
     * Return a string representation of this LatLng object
     * @return string
     */
    public function __toString()
    {
        return "({$this->lat}, {$this->lng})";
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Returns an array containing the point
     *
     * @return array
     */
    public function getPoints()
    {
        return [$this->getLng(), $this->getLat()];
    }

    /**
     * @return float
     */
    public function getH()
    {
        return $this->h;
    }

    /**
     * @return Ellipsoid
     */
    public function getEllipsoid()
    {
        return $this->ellipsoid;
    }

    /**
     * @return Ellipsoid
     */
    public function setEllipsoid(Ellipsoid $ellipsoid)
    {
        $this->ellipsoid = $ellipsoid;
    }

    /**
     * Calculates the distance between the given coordinate
     * and this coordinate.
     *
     * @param Coordinate $coordinate
     * @param DistanceInterface $calculator instance of distance calculation class
     *
     * @return float
     */
    public function getDistance(Coordinate $coordinate, DistanceInterface $calculator)
    {
        return $calculator->getDistance($this, $coordinate);
    }

    /**
     * @param FormatterInterface $formatter
     *
     * @return mixed
     */
    public function format(FormatterInterface $formatter)
    {
        return $formatter->format($this);
    }

    /**
     * Validates latitude
     *
     * @param mixed $latitude
     *
     * @return bool
     */
    protected function isValidLatitude($latitude)
    {
        return $this->isNumericInBounds($latitude, - 90.0, 90.0);
    }

    /**
     * Validates longitude
     *
     * @param mixed $longitude
     *
     * @return bool
     */
    protected function isValidLongitude($longitude)
    {
        return $this->isNumericInBounds($longitude, - 180.0, 180.0);
    }

    /**
     * Checks if the given value is (1) numeric, and (2) between lower
     * and upper bounds (including the bounds values).
     *
     * @param float $value
     * @param float $lower
     * @param float $upper
     *
     * @return bool
     */
    protected function isNumericInBounds($value, $lower, $upper)
    {
        if (! is_numeric($value)) {
            return false;
        }

        if ($value < $lower || $value > $upper) {
            return false;
        }

        return true;
    }
}
