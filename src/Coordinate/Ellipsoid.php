<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Coordinate;

use League\Geotools\Exception\InvalidArgumentException;
use League\Geotools\Exception\NotMatchingEllipsoidException;

/**
 * Ellipsoid class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 *
 * @see    http://en.wikipedia.org/wiki/Reference_ellipsoid
 * @see    http://www.colorado.edu/geography/gcraft/notes/datum/gif/ellipse.gif
 */
class Ellipsoid
{
    /**
     * List of selected reference ellipsoids.
     *
     * @var string
     */
    const AIRY                  = 'AIRY';
    const AUSTRALIAN_NATIONAL   = 'AUSTRALIAN_NATIONAL';
    const BESSEL_1841           = 'BESSEL_1841';
    const BESSEL_1841_NAMBIA    = 'BESSEL_1841_NAMBIA';
    const CLARKE_1866           = 'CLARKE_1866';
    const CLARKE_1880           = 'CLARKE_1880';
    const EVEREST               = 'EVEREST';
    const FISCHER_1960_MERCURY  = 'FISCHER_1960_MERCURY';
    const FISCHER_1968          = 'FISCHER_1968';
    const GRS_1967              = 'GRS_1967';
    const GRS_1980              = 'GRS_1980';
    const HELMERT_1906          = 'HELMERT_1906';
    const HOUGH                 = 'HOUGH';
    const INTERNATIONAL         = 'INTERNATIONAL';
    const KRASSOVSKY            = 'KRASSOVSKY';
    const MODIFIED_AIRY         = 'MODIFIED_AIRY';
    const MODIFIED_EVEREST      = 'MODIFIED_EVEREST';
    const MODIFIED_FISCHER_1960 = 'MODIFIED_FISCHER_1960';
    const SOUTH_AMERICAN_1969   = 'SOUTH_AMERICAN_1969';
    const WGS60                 = 'WGS60';
    const WGS66                 = 'WGS66';
    const WGS72                 = 'WGS72';
    const WGS84                 = 'WGS84';

    /**
     * The name of the Ellipsoid.
     *
     * @var string
     */
    protected $name;

    /**
     * The semi-major axis (equatorial radius) in meters.
     * @see http://en.wikipedia.org/wiki/Earth_radius
     * @see http://home.online.no/~sigurdhu/WGS84_Eng.html
     *
     * @var double
     */
    protected $a;

    /**
     * The inverse flattening.
     * @see http://home.online.no/~sigurdhu/WGS84_Eng.html
     *
     * @var double
     */
    protected $invF;

    /**
     * Selected reference ellipsoids.
     * Source: Defense Mapping Agency. 1987b. Washington, DC: Defense Mapping Agency
     * DMA Technical Report: Supplement to Department of Defense World Geodetic System 1984 Technical Report.
     * @see http://en.wikipedia.org/wiki/Geodetic_datum
     * @see http://www.colorado.edu/geography/gcraft/notes/datum/gif/refellip.gif
     *
     * @var array
     */
    protected static $referenceEllipsoids = array(
        self::AIRY => array(
            'name' => 'Airy',
            'a'    => 6377563.396,
            'invF' => 299.3249646,
        ),
        self::AUSTRALIAN_NATIONAL => array(
            'name' => 'Australian National',
            'a'    => 6378160.0,
            'invF' => 298.25,
        ),
        self::BESSEL_1841 => array(
            'name' => 'Bessel 1841',
            'a'    => 6377397.155,
            'invF' => 299.1528128,
        ),
        self::BESSEL_1841_NAMBIA => array(
            'name' => 'Bessel 1841 (Nambia)',
            'a'    => 6377483.865,
            'invF' => 299.1528128,
        ),
        self::CLARKE_1866 => array(
            'name' => 'Clarke 1866',
            'a'    => 6378206.4,
            'invF' => 294.9786982,
        ),
        self::CLARKE_1880 => array(
            'name' => 'Clarke 1880',
            'a'    => 6378249.145,
            'invF' => 293.465,
        ),
        self::EVEREST => array(
            'name' => 'Everest',
            'a'    => 6377276.345,
            'invF' => 300.8017,
        ),
        self::FISCHER_1960_MERCURY => array(
            'name' => 'Fischer 1960 (Mercury)',
            'a'    => 6378166.0,
            'invF' => 298.3,
        ),
        self::FISCHER_1968 => array(
            'name' => 'Fischer 1968',
            'a'    => 6378150.0,
            'invF' => 298.3,
        ),
        self::GRS_1967 => array(
            'name' => 'GRS 1967',
            'a'    => 6378160.0,
            'invF' => 298.247167427,
        ),
        self::GRS_1980 => array(
            'name' => 'GRS 1980',
            'a'    => 6378137,
            'invF' => 298.257222101,
        ),
        self::HELMERT_1906 => array(
            'name' => 'Helmert 1906',
            'a'    => 6378200.0,
            'invF' => 298.3,
        ),
        self::HOUGH => array(
            'name' => 'Hough',
            'a'    => 6378270.0,
            'invF' => 297.0,
        ),
        self::INTERNATIONAL => array(
            'name' => 'International',
            'a'    => 6378388.0,
            'invF' => 297.0,
        ),
        self::KRASSOVSKY => array(
            'name' => 'Krassovsky',
            'a'    => 6378245.0,
            'invF' => 298.3,
        ),
        self::MODIFIED_AIRY => array(
            'name' => 'Modified Airy',
            'a'    => 6377340.189,
            'invF' => 299.3249646,
        ),
        self::MODIFIED_EVEREST => array(
            'name' => 'Modified Everest',
            'a'    => 6377304.063,
            'invF' => 300.8017,
        ),
        self::MODIFIED_FISCHER_1960 => array(
            'name' => 'Modified Fischer 1960',
            'a'    => 6378155.0,
            'invF' => 298.3,
        ),
        self::SOUTH_AMERICAN_1969 => array(
            'name' => 'South American 1969',
            'a'    => 6378160.0,
            'invF' => 298.25,
        ),
        self::WGS60 => array(
            'name' => 'WGS 60',
            'a'    => 6378165.0,
            'invF' => 298.3,
        ),
        self::WGS66 => array(
            'name' => 'WGS 66',
            'a'    => 6378145.0,
            'invF' => 298.25,
        ),
        self::WGS72 => array(
            'name' => 'WGS 72',
            'a'    => 6378135.0,
            'invF' => 298.26,
        ),
        self::WGS84 => array(
            'name' => 'WGS 84',
            'a'    => 6378137.0,
            'invF' => 298.257223563,
        ),
    );


    /**
     * Create a new ellipsoid.
     *
     * @param string $name The name of the ellipsoid to create.
     * @param double $a    The semi-major axis (equatorial radius) in meters.
     * @param double $invF The inverse flattening.
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name, $a, $invF)
    {
        if (0.0 >= (double) $invF) {
            throw new InvalidArgumentException('The inverse flattening cannot be negative or equal to zero !');
        }

        $this->name = $name;
        $this->a    = $a;
        $this->invF = $invF;
    }

    /**
     * Create the ellipsoid chosen by its name.
     *
     * @param string $name The name of the ellipsoid to create (optional).
     *
     * @return Ellipsoid
     */
    public static function createFromName($name = self::WGS84)
    {
        $name = trim($name);

        if (empty($name)) {
            throw new InvalidArgumentException('Please provide an ellipsoid name !');
        }

        if (!array_key_exists($name, self::$referenceEllipsoids)) {
            throw new InvalidArgumentException(
                sprintf('%s ellipsoid does not exist in selected reference ellipsoids !', $name)
            );
        }

        return self::createFromArray(self::$referenceEllipsoids[$name]);
    }

    /**
     * Create an ellipsoid from an array.
     *
     * @param array $newEllipsoid The ellipsoid's parameters to create.
     *
     * @return Ellipsoid
     */
    public static function createFromArray(array $newEllipsoid)
    {
        if (!isset($newEllipsoid['name']) || !isset($newEllipsoid['a']) || !isset($newEllipsoid['invF'])
            || 3 !== count($newEllipsoid)) {
            throw new InvalidArgumentException('Ellipsoid arrays should contain `name`, `a` and `invF` keys !');
        }

        return new self($newEllipsoid['name'], $newEllipsoid['a'], $newEllipsoid['invF']);
    }

    /**
     * Check if coordinates have the same ellipsoid.
     *
     * @param CoordinateInterface $a A coordinate.
     * @param CoordinateInterface $b A coordinate.
     *
     * @throws NotMatchingEllipsoidException
     */
    public static function checkCoordinatesEllipsoid(CoordinateInterface $a, CoordinateInterface $b)
    {
        if ($a->getEllipsoid() != $b->getEllipsoid()) {
            throw new NotMatchingEllipsoidException('The ellipsoids for both coordinates must match !');
        }
    }

    /**
     * Returns the ellipsoid's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the semi-major axis (equatorial radius) in meters.
     *
     * @return double
     */
    public function getA()
    {
        return (double) $this->a;
    }

    /**
     * Computes and returns the semi-minor axis (polar distance) in meters.
     * @see http://home.online.no/~sigurdhu/WGS84_Eng.html
     *
     * @return double
     */
    public function getB()
    {
        return (double) $this->a * (1 - 1 / $this->invF);
    }

    /**
     * Returns the inverse flattening.
     *
     * @return double
     */
    public function getInvF()
    {
        return (double) $this->invF;
    }

    /**
     * Computes and returns the arithmetic mean radius in meters.
     * @see http://home.online.no/~sigurdhu/WGS84_Eng.html
     *
     * @return double
     */
    public function getArithmeticMeanRadius()
    {
        return (double) $this->a * (1 - 1 / $this->invF / 3);
    }

    /**
     * Returns the list of available ellipsoids sorted by alphabetical order.
     *
     * @return string The list of available ellipsoids comma separated.
     */
    public static function getAvailableEllipsoidNames()
    {
        ksort(self::$referenceEllipsoids);

        return implode(', ', array_keys(self::$referenceEllipsoids));
    }
}
