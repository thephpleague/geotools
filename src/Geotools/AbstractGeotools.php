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
    * The radius of the Earth in meters.
    * @see http://en.wikipedia.org/wiki/Earth_radius
    *
    * @var double
    */
    const EARTH_RADIUS = 6378136.047;

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
