<?php
/**
 * Interface for simplifying a polyline
 */

namespace Webbing\Nomadlog\Location\Processor\Polyline;

use Webbing\Nomadlog\Location\Polyline;

/**
 * Interface for simplifying a polyline
 */
interface SimplifyInterface
{
    /**
     * Simplifies the given polyline
     *
     * @param \Webbing\Nomadlog\Location\Polyline $polyline
     *
     * @return Polyline
     */
    public function simplify(Polyline $polyline);
}
