<?php
/**
 * GeoJSON Polyline Formatter
 */

namespace Webbing\Nomadlog\Location\Formatter\Polyline;

use Webbing\Nomadlog\GeoJson\Geometry\LineString;
use Webbing\Nomadlog\GeoJson\Geometry\Point;
use Webbing\Nomadlog\Location\Polyline;

/**
 * GeoJSON Polyline Formatter
 */
class GeoJSON implements FormatterInterface
{
    /**
     * @param \Webbing\Nomadlog\Location\Polyline $polyline
     *
     * @return string
     */
    public function format(Polyline $polyline)
    {
        $points = [];

        foreach ($polyline->getPoints() as $point) {
            /** @var \Webbing\Nomadlog\Location\Coordinate $point */
            $points[] = new Point($point->getPoints());
        }

        return new LineString($points);
    }
}
