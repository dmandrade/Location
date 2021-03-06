<?php
/**
 * Polyline Implementation
 */

namespace Webbing\Nomadlog\Location;

use Webbing\Nomadlog\Location\Distance\DistanceInterface;
use Webbing\Nomadlog\Location\Formatter\Polyline\FormatterInterface;

/**
 * Polyline Implementation
 */
class Polyline implements GeometryInterface
{
    /**
     * @var array
     */
    protected $points = [];

    /**
     * @param Coordinate $point
     */
    public function addPoint(Coordinate $point)
    {
        $this->points[] = $point;
    }

    /**
     * @return array
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @return int
     */
    public function getNumberOfPoints()
    {
        return count($this->points);
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
     * @return Line[]|array
     */
    public function getSegments()
    {
        $segments = [];

        if (count($this->points) <= 1) {
            return $segments;
        }

        $previousPoint = reset($this->points);

        while ($point = next($this->points)) {
            $segments[]    = new Line($previousPoint, $point);
            $previousPoint = $point;
        }

        return $segments;
    }

    /**
     * Calculates the length of the polyline.
     *
     * @param DistanceInterface $calculator instance of distance calculation class
     *
     * @return float
     */
    public function getLength(DistanceInterface $calculator)
    {
        $distance = 0.0;

        if (count($this->points) <= 1) {
            return $distance;
        }

        foreach ($this->getSegments() as $segment) {
            $distance += $segment->getLength($calculator);
        }

        return $distance;
    }

    /**
     * Create a new polyline with reversed order of points, i. e. reversed
     * polyline direction.
     *
     * @return Polyline
     */
    public function getReverse()
    {
        $reversed = new static();

        foreach (array_reverse($this->points) as $point) {
            $reversed->addPoint($point);
        }

        return $reversed;
    }
}
