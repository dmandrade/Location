<?php
/**
 * GeoJSON Coordinate Formatter
 */

namespace Webbing\Nomadlog\Location\Formatter\Coordinate;

use Webbing\Nomadlog\GeoJson\Geometry\Point;
use Webbing\Nomadlog\Location\Coordinate;

/**
 * GeoJSON Coordinate Formatter
 */
class GeoJSON implements FormatterInterface
{
    /**
     * @param Coordinate $coordinate
     *
     * @return string
     */
    public function format(Coordinate $coordinate)
    {
        return new Point($coordinate->getPoints());
    }
}
