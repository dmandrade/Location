<?php
/**
 * Simplify Polyline
 */

namespace Webbing\Nomadlog\Location\Processor\Polyline;

use Webbing\Nomadlog\Location\Coordinate;
use Webbing\Nomadlog\Location\Line;
use Webbing\Nomadlog\Location\Polyline;

/**
 * Simplify Polyline
 *
 * @deprecated This class is no longer supported. Please use
 * the `SimplifyDouglasPeucker` oder `SimplifyBearing` classes.
 */
class Simplify
{
    /**
     * @var \Webbing\Nomadlog\Location\Polyline
     */
    protected $polyline;

    /**
     * @param Polyline $polyline
     */
    public function __construct(Polyline $polyline)
    {
        $this->polyline = $polyline;
    }

    /**
     * @param float $tolerance The maximum allowed deviation
     *
     * @return Polyline
     */
    public function simplify($tolerance)
    {
        $simplifiedLine = $this->douglasPeucker(
            $this->polyline->getPoints(),
            $tolerance
        );

        $resultPolyline = new Polyline();

        foreach ($simplifiedLine as $point) {
            $resultPolyline->addPoint($point);
        }

        return $resultPolyline;
    }

    /**
     * @param array $line
     * @param float $tolerance
     *
     * @return array
     */
    protected function douglasPeucker($line = [], $tolerance)
    {
        $distanceMax = 0;
        $index       = 0;

        $lineSize = count($line);

        for ($i = 1; $i <= ($lineSize - 1); $i ++) {
            $distance = $this->getPerpendicularDistance($line[$i], new Line($line[0], $line[$lineSize - 1]));

            if ($distance > $distanceMax) {
                $index       = $i;
                $distanceMax = $distance;
            }
        }

        if ($distanceMax > $tolerance) {
            $lineSplitFirst  = array_slice($line, 0, $index);
            $lineSplitSecond = array_slice($line, $index, $lineSize);

            $recursiveResultsSplitFirst  = $this->douglasPeucker($lineSplitFirst, $tolerance);
            $recursiveResultsSplitSecond = $this->douglasPeucker($lineSplitSecond, $tolerance);

            array_pop($recursiveResultsSplitFirst);

            return array_merge($recursiveResultsSplitFirst, $recursiveResultsSplitSecond);
        }

        return [$line[0], $line[$lineSize - 1]];
    }

    /**
     * @param Coordinate $point
     * @param Line $line
     *
     * @return number
     */
    protected function getPerpendicularDistance(Coordinate $point, Line $line)
    {
        $ellipsoid = $point->getEllipsoid();

        $ellipsoidRadius = $ellipsoid->getArithmeticMeanRadius();

        $firstLinePointLat = $this->deg2radLatitude($line->getPoint1()->getLat());
        $firstLinePointLng = $this->deg2radLongitude($line->getPoint1()->getLng());

        $firstLinePointX = $ellipsoidRadius * cos($firstLinePointLng) * sin($firstLinePointLat);
        $firstLinePointY = $ellipsoidRadius * sin($firstLinePointLng) * sin($firstLinePointLat);
        $firstLinePointZ = $ellipsoidRadius * cos($firstLinePointLat);

        $secondLinePointLat = $this->deg2radLatitude($line->getPoint2()->getLat());
        $secondLinePointLng = $this->deg2radLongitude($line->getPoint2()->getLng());

        $secondLinePointX = $ellipsoidRadius * cos($secondLinePointLng) * sin($secondLinePointLat);
        $secondLinePointY = $ellipsoidRadius * sin($secondLinePointLng) * sin($secondLinePointLat);
        $secondLinePointZ = $ellipsoidRadius * cos($secondLinePointLat);

        $pointLat = $this->deg2radLatitude($point->getLat());
        $pointLng = $this->deg2radLongitude($point->getLng());

        $pointX = $ellipsoidRadius * cos($pointLng) * sin($pointLat);
        $pointY = $ellipsoidRadius * sin($pointLng) * sin($pointLat);
        $pointZ = $ellipsoidRadius * cos($pointLat);

        $normalizedX = $firstLinePointY * $secondLinePointZ - $firstLinePointZ * $secondLinePointY;
        $normalizedY = $firstLinePointZ * $secondLinePointX - $firstLinePointX * $secondLinePointZ;
        $normalizedZ = $firstLinePointX * $secondLinePointY - $firstLinePointY * $secondLinePointX;

        $length = sqrt($normalizedX * $normalizedX + $normalizedY * $normalizedY + $normalizedZ * $normalizedZ);

        $normalizedX /= $length;
        $normalizedY /= $length;
        $normalizedZ /= $length;

        $thetaPoint = $normalizedX * $pointX + $normalizedY * $pointY + $normalizedZ * $pointZ;

        $length = sqrt($pointX * $pointX + $pointY * $pointY + $pointZ * $pointZ);

        $thetaPoint /= $length;

        $distance = abs((M_PI / 2) - acos($thetaPoint));

        return $distance * $ellipsoidRadius;
    }

    /**
     * @param float $latitude
     *
     * @return float
     */
    protected function deg2radLatitude($latitude)
    {
        return deg2rad(90 - $latitude);
    }

    /**
     * @param float $longitude
     *
     * @return float
     */
    protected function deg2radLongitude($longitude)
    {
        if ($longitude > 0) {
            return deg2rad($longitude);
        }

        return deg2rad($longitude + 360);
    }
}
