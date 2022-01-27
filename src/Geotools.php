<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools;

use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Distance\Distance;
use League\Geotools\Vertex\Vertex;
use League\Geotools\Batch\Batch;
use League\Geotools\Geohash\Geohash;
use League\Geotools\Convert\Convert;
use Geocoder\Geocoder;

/**
 * Geotools class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Geotools implements GeotoolsInterface
{
    /**
     * The cardinal points / directions (the four cardinal directions,
     * the four ordinal directions, plus eight further divisions).
     *
     * @var array
     */
    public static $cardinalPoints = array(
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
    public static $latitudeBands = array(
        'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'X'
    );


    /**
     * {@inheritDoc}
     */
    public function distance()
    {
        return new Distance;
    }

    /**
     * {@inheritDoc}
     */
    public function vertex()
    {
        return new Vertex;
    }

    /**
     * {@inheritDoc}
     */
    public function batch(Geocoder $geocoder)
    {
        return new Batch($geocoder);
    }

    /**
     * {@inheritDoc}
     */
    public function geohash()
    {
        return new Geohash;
    }

    /**
     * {@inheritDoc}
     */
    public function convert(CoordinateInterface $coordinates)
    {
        return new Convert($coordinates);
    }
}
