<?php
/**
 * Polygon Formatter Interface
 */

namespace Webbing\Nomadlog\Location\Formatter\Polygon;

use Webbing\Nomadlog\Location\Polygon;

/**
 * Polygon Formatter Interface
 */
interface FormatterInterface
{
    /**
     * @param Polygon $polygon
     *
     * @return mixed
     */
    public function format(Polygon $polygon);
}
