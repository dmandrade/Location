<?php
/**
 * Simplify Polyline with the Douglas-Peucker-Algorithm
 *
 * The Algorithm is described here:
 * http://en.wikipedia.org/wiki/Ramer%E2%80%93Douglas%E2%80%93Peucker_algorithm
 */

namespace Webbing\Nomadlog\Location\Processor\Polyline;

use Webbing\Nomadlog\Location\Line;
use Webbing\Nomadlog\Location\Polyline;
use Webbing\Nomadlog\Location\Utility\PerpendicularDistance;

/**
 * Simplify Polyline with the Douglas-Peucker-Algorithm
 */
class SimplifyDouglasPeucker implements SimplifyInterface
{
    /**
     * @var float
     */
    private $tolerance;

    /**
     * @param float $tolerance the perpendicular distance threshold in meters
     */
    public function __construct($tolerance)
    {
        $this->tolerance = $tolerance;
    }

    /**
     * @param \Webbing\Nomadlog\Location\Polyline $polyline
     *
     * @return \Webbing\Nomadlog\Location\Polyline
     */
    public function simplify(Polyline $polyline)
    {
        $resultPolyline = new Polyline();
        $simplifiedLine = $this->douglasPeucker($polyline->getPoints());

        foreach ($simplifiedLine as $point) {
            $resultPolyline->addPoint($point);
        }

        return $resultPolyline;
    }

    /**
     * @param array $line
     *
     * @return array
     */
    protected function douglasPeucker(array $line)
    {
        $distanceMax = 0;
        $index       = 0;

        $lineSize = count($line);

        $pdCalc = new PerpendicularDistance();

        for ($i = 1; $i <= ($lineSize - 1); $i ++) {
            $distance = $pdCalc->getPerpendicularDistance($line[$i], new Line($line[0], $line[$lineSize - 1]));

            if ($distance > $distanceMax) {
                $index       = $i;
                $distanceMax = $distance;
            }
        }

        if ($distanceMax > $this->tolerance) {
            $lineSplitFirst  = array_slice($line, 0, $index);
            $lineSplitSecond = array_slice($line, $index, $lineSize);

            $resultsSplit1  = $this->douglasPeucker($lineSplitFirst);
            $resultsSplit2 = $this->douglasPeucker($lineSplitSecond);

            array_pop($resultsSplit1);

            return array_merge($resultsSplit1, $resultsSplit2);
        }

        return [$line[0], $line[$lineSize - 1]];
    }
}
