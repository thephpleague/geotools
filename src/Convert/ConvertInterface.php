<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Convert;

/**
 * Convert interface
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface ConvertInterface
{
    /**
     * Round precision for decimal minutes values.
     *
     * @var integer
     */
    const DECIMAL_MINUTES_PRECISION = 5;

    /**
     * Round precision for degree decimal minutes values.
     *
     * @var integer
     */
    const DEGREE_DECIMAL_MINUTES_PRECISION = 3;

    /**
     * Round mode ofr decimal minutes values.
     *
     * @var string
     */
    const DECIMAL_MINUTES_MODE = PHP_ROUND_HALF_EVEN;

    /**
     * The default format for degrees minutes seconds coordinates.
     *
     * @var string
     */
    const DEFAULT_DMS_FORMAT = '%D°%M′%S″%L, %d°%m′%s″%l';

    /**
     * The default format for decimal minutes coordinates.
     *
     * @var string
     */
    const DEFAULT_DM_FORMAT = '%P%D %N%L, %p%d %n%l';

    /**
     * The default format for degree decimal minutes coordinates.
     *
     * @var string
     */
    const DEFAULT_DDM_FORMAT = '%L %P%D° %N %l %p%d° %n';

    /**
     * The sign of the latitude.
     *
     * @var string
     */
    const LATITUDE_SIGN = '%P';

    /**
     * The direction of the latitude.
     *
     * @var string
     */
    const LATITUDE_DIRECTION = '%L';

    /**
     * Latitude in degrees.
     *
     * @var string
     */
    const LATITUDE_DEGREES = '%D';

    /**
     * Latitude in minutes.
     *
     * @var string
     */
    const LATITUDE_MINUTES = '%M';

    /**
     * Latitude in decimal minutes.
     *
     * @var string
     */
    const LATITUDE_DECIMAL_MINUTES = '%N';

    /**
     * Latitude in seconds.
     *
     * @var string
     */
    const LATITUDE_SECONDS = '%S';

    /**
     * The sign of the longitude.
     *
     * @var string
     */
    const LONGITUDE_SIGN = '%p';

    /**
     * The direction of the longitude.
     *
     * @var string
     */
    const LONGITUDE_DIRECTION = '%l';

    /**
     * Longitude in degrees.
     *
     * @var string
     */
    const LONGITUDE_DEGREES = '%d';

    /**
     * Longitude in minutes.
     *
     * @var string
     */
    const LONGITUDE_MINUTES = '%m';

    /**
     * Longitude in decimal minutes.
     *
     * @var string
     */
    const LONGITUDE_DECIMAL_MINUTES = '%n';

    /**
     * Longitude in seconds.
     *
     * @var string
     */
    const LONGITUDE_SECONDS = '%s';

    /**
     * Convert and format a decimal degree coordinate to degrees minutes seconds coordinate.
     *
     * @param string $format The way to format the DMS coordinate.
     *
     * @return string Converted and formatted string.
     */
    public function toDegreesMinutesSeconds($format = ConvertInterface::DEFAULT_DMS_FORMAT);

    /**
     * Convert and format a decimal degree coordinate to decimal minutes coordinate.
     *
     * @param string $format The way to format the DMS coordinate.
     *
     * @return string Converted and formatted string.
     */
    public function toDecimalMinutes($format = ConvertInterface::DEFAULT_DM_FORMAT);

    /**
     * Convert and format a decimal degree coordinate to degree decimal minutes coordinate.
     *
     * @param string $format The way to format the DDM coordinate.
     *
     * @return string Converted and formatted string.
     */
    public function toDegreeDecimalMinutes($format = ConvertInterface::DEFAULT_DDM_FORMAT);

    /**
     * Converts a WGS84 decimal degrees coordinate in the Universal Transverse Mercator projection (UTM).
     *
     * @return string The converted UTM coordinate in meters.
     *
     * @see http://www.uwgb.edu/dutchs/UsefulData/UTMFormulas.HTM
     * @see http://en.wikipedia.org/wiki/Universal_Transverse_Mercator_coordinate_system
     */
    public function toUniversalTransverseMercator();
}
