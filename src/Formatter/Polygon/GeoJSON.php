<?php
/**
 * GeoJSON Polygon Formatter
 *
 * @author    Richard Barnes <rbarnes@umn.edu>
 * @license   https://opensource.org/licenses/GPL-3.0 GPL
 * @link      https://github.com/mjaschen/phpgeo
 */

namespace Webbing\Nomadlog\Location\Formatter\Polygon;

use Webbing\Nomadlog\Location\Polygon;

/**
 * GeoJSON Polygon Formatter
 *
 * @author   Richard Barnes <rbarnes@umn.edu>
 * @license  https://opensource.org/licenses/GPL-3.0 GPL
 * @link     https://github.com/mjaschen/phpgeo
 */
class GeoJSON implements FormatterInterface
{
    /**
     * @param \Webbing\Nomadlog\Location\Polygon $polygon
     *
     * @return string
     */
    public function format(Polygon $polygon)
    {
        $points = [];

        foreach ($polygon->getPoints() as $point) {
            $points[] = [$point->getLng(), $point->getLat()];
        }

        return json_encode(
            [
                'type'        => 'Polygon',
                'coordinates' => $points,
            ]
        );
    }
}
