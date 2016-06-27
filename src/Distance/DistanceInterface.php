<?php
/**
 * Interface for Distance Calculator Classes
 */

namespace Webbing\Nomadlog\Location\Distance;

use Webbing\Nomadlog\Location\Coordinate;

/**
 * Interface for Distance Calculator Classes
 */
interface DistanceInterface
{
    /**
     * @param Coordinate $point1
     * @param Coordinate $point2
     *
     * @return float distance between the two coordinates in meters
     */
    public function getDistance(Coordinate $point1, Coordinate $point2);
}
