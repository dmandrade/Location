<?php

namespace Webbing\Nomadlog\Location;

interface GeometryInterface
{
    /**
     * Returns an array containing all assigned points.
     *
     * @return array
     */
    public function getPoints();
}
