<?php
/**
 * Coordinate Bounds Implementation
 */

namespace Webbing\Nomadlog\Location;

use Webbing\Nomadlog\Location\Coordinate;

/**
 * Coordinate Bounds Implementation
 */
class Bounds
{
    /**
     * @var Coordinate
     */
    protected $northWest;

    /**
     * @var Coordinate
     */
    protected $southEast;

    /**
     * @param Coordinate $northWest
     * @param Coordinate $southEast
     */
    public function __construct(Coordinate $northWest, Coordinate $southEast)
    {
        $this->northWest = $northWest;
        $this->southEast = $southEast;
    }

    /**
     * Getter
     *
     * @return Coordinate
     */
    public function getNorthWest()
    {
        return $this->northWest;
    }

    /**
     * Getter
     *
     * @return Coordinate
     */
    public function getSouthEast()
    {
        return $this->southEast;
    }

    /**
     * @return float
     */
    public function getNorth()
    {
        return $this->northWest->getLat();
    }

    /**
     * @return float
     */
    public function getSouth()
    {
        return $this->southEast->getLat();
    }

    /**
     * @return float
     */
    public function getWest()
    {
        return $this->northWest->getLng();
    }

    /**
     * @return float
     */
    public function getEast()
    {
        return $this->southEast->getLng();
    }

    /**
     * Calculates the center of this bounds object and returns it as a
     * Coordinate instance.
     *
     * @return Coordinate
     */
    public function getCenter()
    {
        $centerLat = ($this->getNorth() + $this->getSouth()) / 2;

        return new Coordinate($centerLat, $this->getCenterLng());
    }

    /**
     * @return float
     */
    protected function getCenterLng()
    {
        $centerLng = ($this->getEast() + $this->getWest()) / 2;

        $overlap = $this->getWest() > 0 && $this->getEast() < 0;

        if ($overlap && $centerLng > 0) {
            return -180 + $centerLng;
        }

        if ($overlap && $centerLng < 0) {
            return 180 + $centerLng;
        }

        if ($overlap && $centerLng == 0) {
            return 180;
        }

        return $centerLng;
    }
}
