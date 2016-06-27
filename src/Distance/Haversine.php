<?php
/**
 * Implementation of distance calculation
 *
 * @see      http://en.wikipedia.org/wiki/Law_of_haversines
 */

namespace Webbing\Nomadlog\Location\Distance;

use Webbing\Nomadlog\Location\Coordinate;
use Webbing\Nomadlog\Location\Exception\NotConvergingException;
use Webbing\Nomadlog\Location\Exception\NotMatchingEllipsoidException;

/**
 * Implementation of distance calculation
 *
 * @see      http://en.wikipedia.org/wiki/Law_of_haversines
 */
class Haversine implements DistanceInterface
{
    /**
     * @param Coordinate $point1
     * @param Coordinate $point2
     *
     * @throws NotMatchingEllipsoidException
     *
     * @return float
     */
    public function getDistance(Coordinate $point1, Coordinate $point2)
    {
        if ($point1->getEllipsoid() != $point2->getEllipsoid()) {
            throw new NotMatchingEllipsoidException("The ellipsoids for both coordinates must match");
        }

        $radius = $point1->getEllipsoid()->getArithmeticMeanRadius();

        $lat1 = deg2rad($point1->getLat());
        $lat2 = deg2rad($point2->getLat());
        $lng1 = deg2rad($point1->getLng());
        $lng2 = deg2rad($point2->getLng());

        /*$dLat = $lat2 - $lat1;
        $dLng = $lng2 - $lng1;

        $distance = 2 * $radius * asin(
            sqrt(
                pow(sin($dLat / 2), 2)
                + cos($lat1) * cos($lat2) * pow(sin($dLng / 2), 2)
            )
        );*/
        $distance = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng2 - $lng1)) * $radius;

        return round($distance, 3);
    }
}
