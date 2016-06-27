<?php
/**
 * Coordinate Factory
 */

namespace Webbing\Nomadlog\Location\Factory;

use Webbing\Nomadlog\Location\Coordinate;
use Webbing\Nomadlog\Location\Ellipsoid;

/**
 * Coordinate Factory
 */
class CoordinateFactory implements GeometryFactoryInterface
{
    /**
     * Creates a Coordinate instance from the given string.
     *
     * The string is parsed by a regular expression for a known
     * format of geographical coordinates.
     *
     * @param string $string formatted geographical coordinate
     * @param \Webbing\Nomadlog\Location\Ellipsoid $ellipsoid
     *
     * @return \Webbing\Nomadlog\Location\Coordinate
     */
    public static function fromString($string, Ellipsoid $ellipsoid = null)
    {
        // Decimal minutes without cardinal letters, e. g. "52 12.345, 13 23.456",
        // "52° 12.345, 13° 23.456", "52° 12.345′, 13° 23.456′", "52 12.345 N, 13 23.456 E",
        // "N52° 12.345′ E13° 23.456′"
        if (preg_match('/(-?\d{1,2})°?\s+(\d{1,2}\.?\d*)[\'′]?[, ]\s*(-?\d{1,3})°?\s+(\d{1,2}\.?\d*)[\'′]?/ui', $string, $match)) {
            $latitude = $match[1] >= 0 ? $match[1] + $match[2] / 60 : $match[1] - $match[2] / 60;
            $longitude = $match[3] >= 0 ? $match[3] + $match[4] / 60 : $match[3] - $match[4] / 60;

            return new Coordinate($latitude, $longitude, $ellipsoid);
        }

        // Decimal minutes with cardinal letters, e. g. "52 12.345, 13 23.456",
        // "52° 12.345, 13° 23.456", "52° 12.345′, 13° 23.456′", "52 12.345 N, 13 23.456 E",
        // "N52° 12.345′ E13° 23.456′"
        if (preg_match('/([NS]?\s*)(\d{1,2})°?\s+(\d{1,2}\.?\d*)[\'′]?(\s*[NS]?)[, ]\s*([EW]?\s*)(\d{1,3})°?\s+(\d{1,2}\.?\d*)[\'′]?(\s*[EW]?)/ui', $string, $match)) {
            $latitude = $match[2] + $match[3] / 60;
            if (trim(strtoupper($match[1])) === 'S' || trim(strtoupper($match[4])) === 'S') {
                $latitude = - $latitude;
            }
            $longitude = $match[6] + $match[7] / 60;
            if (trim(strtoupper($match[5])) === 'W' || trim(strtoupper($match[8])) === 'W') {
                $longitude = - $longitude;
            }

            return new Coordinate($latitude, $longitude, $ellipsoid);
        }

        // The most simple format: decimal degrees without cardinal letters,
        // e. g. "52.5, 13.5" or "53.25732 14.24984"
        if (preg_match('/(-?\d{1,2}\.?\d*)°?[, ]\s*(-?\d{1,3}\.?\d*)°?/u', $string, $match)) {
            return new Coordinate($match[1], $match[2], $ellipsoid);
        }

        // Decimal degrees with cardinal letters, e. g. "N52.5, E13.5",
        // "40.2S, 135.3485W", or "56.234°N, 157.245°W"
        if (preg_match('/([NS]?\s*)(\d{1,2}\.?\d*)°?(\s*[NS]?)[, ]\s*([EW]?\s*)(\d{1,3}\.?\d*)°?(\s*[EW]?)/ui', $string, $match)) {
            $latitude = $match[2];
            if (trim(strtoupper($match[1])) === 'S' || trim(strtoupper($match[3])) === 'S') {
                $latitude = - $latitude;
            }
            $longitude = $match[5];
            if (trim(strtoupper($match[4])) === 'W' || trim(strtoupper($match[6])) === 'W') {
                $longitude = - $longitude;
            }

            return new Coordinate($latitude, $longitude, $ellipsoid);
        }

        throw new \InvalidArgumentException("Format of coordinates was not recognized");
    }
}
