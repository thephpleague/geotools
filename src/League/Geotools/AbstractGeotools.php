<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools;

/**
 * Geotools abstract class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
abstract class AbstractGeotools
{
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
     * The ratio feet per meter.
     *
     * @var double
     */
    const FEET_PER_METER = 0.3048;

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
    const MILE_UNIT = 'mi';

    /**
     * The feet unit.
     *
     * @var string
     */
    const FOOT_UNIT = 'ft';

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
     * Latitude bands in the UTM coordinate system.
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
