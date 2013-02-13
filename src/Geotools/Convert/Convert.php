<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Convert;

use Geotools\Coordinate\CoordinateInterface;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Convert implements ConvertInterface
{
    /**
     * The coordinate to convert.
     *
     * @var CoordinateInterface
     */
    protected $coordinates;


    /**
     * Set the coordinate to convert.
     *
     * @param CoordinateInterface $coordinates The coordinate to convert.
     */
    public function __construct(CoordinateInterface $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * Parse decimal degrees coordinate to degrees minutes seconds and decimal minutes coordinate.
     *
     * @param string $coordinate The coordinate to parse.
     *
     * @return array The replace pairs values.
     */
    private function parseCoordinate($coordinate)
    {
        list($degrees) = explode('.', abs($coordinate));
        list($minutes) = explode('.', (abs($coordinate) - $degrees) * 60);

        return array(
            'positive'       => $coordinate >= 0,
            'degrees'        => (string) $degrees,
            'decimalMinutes' => (string) round((abs($coordinate) - $degrees) * 60,
                ConvertInterface::DECIMAL_MINUTES_PRECISION,
                ConvertInterface::DECIMAL_MINUTES_MODE),
            'minutes'        => (string) $minutes,
            'seconds'        => (string) round(((abs($coordinate) - $degrees) * 60 - $minutes) * 60),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toDegreesMinutesSeconds($format = ConvertInterface::DEFAULT_DMS_FORMAT)
    {
        $latitude  = $this->parseCoordinate($this->coordinates->getLatitude());
        $longitude = $this->parseCoordinate($this->coordinates->getLongitude());

        return strtr($format, array(
            ConvertInterface::LATITUDE_SIGN       => $latitude['positive'] ? '' : '-',
            ConvertInterface::LATITUDE_DIRECTION  => $latitude['positive'] ? 'N' : 'S',
            ConvertInterface::LATITUDE_DEGREES    => $latitude['degrees'],
            ConvertInterface::LATITUDE_MINUTES    => $latitude['minutes'],
            ConvertInterface::LATITUDE_SECONDS    => $latitude['seconds'],
            ConvertInterface::LONGITUDE_SIGN      => $longitude['positive'] ? '' : '-',
            ConvertInterface::LONGITUDE_DIRECTION => $longitude['positive'] ? 'E' : 'W',
            ConvertInterface::LONGITUDE_DEGREES   => $longitude['degrees'],
            ConvertInterface::LONGITUDE_MINUTES   => $longitude['minutes'],
            ConvertInterface::LONGITUDE_SECONDS   => $longitude['seconds'],
        ));
    }

    /**
     * Alias of toDegreesMinutesSeconds function.
     *
     * @param string The way to format the DMS coordinate.
     *
     * @return string Converted and formatted string.
     */
    public function toDMS($format = ConvertInterface::DEFAULT_DMS_FORMAT)
    {
        return $this->toDegreesMinutesSeconds($format);
    }

    /**
     * {@inheritDoc}
     */
    public function toDecimalMinutes($format = ConvertInterface::DEFAULT_DM_FORMAT)
    {
        $latitude  = $this->parseCoordinate($this->coordinates->getLatitude());
        $longitude = $this->parseCoordinate($this->coordinates->getLongitude());

        return strtr($format, array(
            ConvertInterface::LATITUDE_SIGN             => $latitude['positive'] ? '' : '-',
            ConvertInterface::LATITUDE_DIRECTION        => $latitude['positive'] ? 'N' : 'S',
            ConvertInterface::LATITUDE_DEGREES          => $latitude['degrees'],
            ConvertInterface::LATITUDE_DECIMAL_MINUTES  => $latitude['decimalMinutes'],
            ConvertInterface::LONGITUDE_SIGN            => $longitude['positive'] ? '' : '-',
            ConvertInterface::LONGITUDE_DIRECTION       => $longitude['positive'] ? 'E' : 'W',
            ConvertInterface::LONGITUDE_DEGREES         => $longitude['degrees'],
            ConvertInterface::LONGITUDE_DECIMAL_MINUTES => $longitude['decimalMinutes'],
        ));
    }

    /**
     * Alias of toDecimalMinutes function.
     *
     * @param string The way to format the DMS coordinate.
     *
     * @return string Converted and formatted string.
     */
    public function toDM($format = ConvertInterface::DEFAULT_DM_FORMAT)
    {
        return $this->toDecimalMinutes($format);
    }
}
