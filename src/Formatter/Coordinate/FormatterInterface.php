<?php
/**
 * Coordinate Formatter Interface
 */

namespace Webbing\Nomadlog\Location\Formatter\Coordinate;

use Webbing\Nomadlog\Location\Coordinate;

/**
 * Coordinate Formatter Interface
 */
interface FormatterInterface
{
    /**
     * @param Coordinate $coordinate
     *
     * @return mixed
     */
    public function format(Coordinate $coordinate);
}
