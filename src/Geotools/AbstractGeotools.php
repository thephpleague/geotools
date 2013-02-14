<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools;

/**
 * Geotools abstract class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
abstract class AbstractGeotools
{
    /**
    * The major radius of the Earth in meters.
    * Ellipsoid model constants (actual values here are for WGS84).
    * @see http://en.wikipedia.org/wiki/Earth_radius
    *
    * @var double
    */
    const EARTH_RADIUS_MAJOR = 6378136.047;

    /**
    * The minor radius of the Earth in meters.
    * Ellipsoid model constants (actual values here are for WGS84).
    * @see http://en.wikipedia.org/wiki/Earth_radius
    *
    * @var double
    */
    const EARTH_RADIUS_MINOR = 6356752.314;

    /**
     * Transverse Mercator is not the same as UTM.
     * A scale factor is required to convert between them.
     *
     * @var double
     */
    const UTM_SCALE_FACTOR = 0.9996;

    /**
     * The ratio meters per mile.
     *
     * @var double
     */
    const METERS_PER_MILE = 1609.344;

    /**
     * The kilometer unit.
     *
     * @var string
     */
    const KILOMETER_UNIT = 'km';

    /**
     * The mile unit.
     *
     * @var string
     */
    const MILE_UNIT = 'mile';

    /**
     * The cardinal points / directions (the four cardinal directions,
     * the four ordinal directions, plus eight further divisions).
     *
     * @var array
     */
    protected $cardinalPoints = array(
        'N', 'NNE', 'NE', 'ENE',
        'E', 'ESE', 'SE', 'SSE',
        'S', 'SSW', 'SW', 'WSW',
        'W', 'WNW', 'NW', 'NNW',
        'N'
    );

    /**
     * Latitude bands in the UTM cordinate system.
     * @see http://en.wikipedia.org/wiki/Universal_Transverse_Mercator_coordinate_system
     *
     * @var array
     */
    protected $latitudeBands = array(
        'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'X'
    );

    /**
     * The origin coordinate.
     *
     * @var CoordinateInterface
     */
    protected $from;

    /**
     * The destination coordinate.
     *
     * @var CoordinateInterface
     */
    protected $to;
}
