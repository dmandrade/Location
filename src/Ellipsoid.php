<?php
/**
 * Ellipsoid Implementation
 */

namespace Webbing\Nomadlog\Location;

/**
 * Ellipsoid Implementation
 */
class Ellipsoid
{
    /**
     * @var string
     */
    protected $name;

    /**
     * major axis
     *
     * @var float
     */
    protected $maj;

    /**
     * Minor axis
     * @var float
     */
    protected $min;

    /**
     * The Inverse Flattening (1/f)
     *
     * @var float
     */
    protected $f;

    /**
     * Eccentricity
     * @var float
     */
    protected $ecc;

    /**
     * Some often used ellipsoids
     *
     * @var array
     */
    protected static $configs = [
        'WGS-84' => [
            'name' => 'World Geodetic System  1984',
            'maj'   => 6378137.0,
            'min'   => 6356752.314245,
            'f'     => 298.257223563,
        ],
        'GRS-80' => [
            'name' => 'Geodetic Reference System 1980',
            'maj'   => 6378137.0,
            'min'   => 6356752.314140,
            'f'     => 298.257222100,
        ],
    ];

    /**
     * @param $name
     * @param $maj
     * @param $min
     * @param $f
     */
    public function __construct($name, $maj, $min, $f)
    {
        $this->name = $name;
        $this->maj  = $maj;
        $this->min  = $min;
        $this->ecc  = (($maj * $maj) - ($min * $min)) / ($maj * $maj);
        $this->f    = $f;
    }

    /**
     * @param string $name
     *
     * @return Ellipsoid
     */
    public static function createDefault($name = 'WGS-84')
    {
        return static::createFromArray(static::$configs[$name]);
    }

    /**
     * @param $config
     *
     * @return Ellipsoid
     */
    public static function createFromArray($config)
    {
        return new static($config['name'], $config['maj'], $config['min'], $config['f']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @return float
     */
    public function getMaj()
    {
        return $this->maj;
    }
    /**
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return float
     */
    public function getA()
    {
        return $this->maj;
    }

    /**
     * Calculation of the semi-minor axis
     *
     * @return float
     */
    public function getB()
    {
        return $this->maj * (1 - 1 / $this->f);
    }

    /**
     * @return float
     */
    public function getEcc()
    {
        return $this->ecc;
    }

    /**
     * @return float
     */
    public function getF()
    {
        return $this->f;
    }

    /**
     * Calculates the arithmetic mean radius
     *
     * @see http://home.online.no/~sigurdhu/WGS84_Eng.html
     *
     * @return float
     */
    public function getArithmeticMeanRadius()
    {
        return ((2 * $this->maj) + $this->min) / 3;
        //return $this->maj * (1 - 1 / $this->f / 3);
    }
}
