<?php
/**
 * Polyline Formatter Interface
 */

namespace Webbing\Nomadlog\Location\Formatter\Polyline;

use Webbing\Nomadlog\Location\Polyline;

/**
 * Polyline Formatter Interface
 */
interface FormatterInterface
{
    /**
     * @param Polyline $polyline
     *
     * @return mixed
     */
    public function format(Polyline $polyline);
}
