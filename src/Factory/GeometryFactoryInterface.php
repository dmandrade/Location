<?php
/**
 * Geometry Factory Interface
 */

namespace Webbing\Nomadlog\Location\Factory;

use Webbing\Nomadlog\Location\GeometryInterface;

/**
 * Geometry Factory Interface
 */
interface GeometryFactoryInterface
{
    /**
     * @param $string
     *
     * @return GeometryInterface
     */
    public static function fromString($string);
}
