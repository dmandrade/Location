<?php
/**
 * GeoJSON Coordinate Formatter
 */

namespace Webbing\Nomadlog\Location\Formatter\Coordinate;

use Webbing\Nomadlog\Location\Coordinate;
use Webbing\Nomadlog\Location\Cartesian as CartesianFormat;

/**
 * GeoJSON Coordinate Formatter
 */
class Cartesian implements FormatterInterface
{
    /**
     * @param Coordinate $coordinate
     *
     * @return string
     */
    public function format(Coordinate $coordinate)
    {
        return CartesianFormat::fromLatLong($coordinate);
    }
}
