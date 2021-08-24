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

use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Geotools;
use League\Geotools\GeotoolsInterface;

/**
 * Convert class
 *
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

        return [
            'positive'       => $coordinate >= 0,
            'degrees'        => (string) $degrees,
            'decimalMinutes' => (string) round((abs($coordinate) - $degrees) * 60,
                ConvertInterface::DECIMAL_MINUTES_PRECISION,
                ConvertInterface::DECIMAL_MINUTES_MODE),
            'minutes'        => (string) $minutes,
            'seconds'        => (string) round(((abs($coordinate) - $degrees) * 60 - $minutes) * 60),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function toDegreesMinutesSeconds($format = ConvertInterface::DEFAULT_DMS_FORMAT)
    {
        $latitude  = $this->parseCoordinate($this->coordinates->getLatitude());
        $longitude = $this->parseCoordinate($this->coordinates->getLongitude());

        return strtr($format, [
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
        ]);
    }

    /**
     * Alias of toDegreesMinutesSeconds function.
     *
     * @param string $format The way to format the DMS coordinate.
     *
     * @deprecated This alias is deprecated, use toDegreesMinutesSeconds()
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

        return strtr($format, [
            ConvertInterface::LATITUDE_SIGN             => $latitude['positive'] ? '' : '-',
            ConvertInterface::LATITUDE_DIRECTION        => $latitude['positive'] ? 'N' : 'S',
            ConvertInterface::LATITUDE_DEGREES          => $latitude['degrees'],
            ConvertInterface::LATITUDE_DECIMAL_MINUTES  => $latitude['decimalMinutes'],
            ConvertInterface::LONGITUDE_SIGN            => $longitude['positive'] ? '' : '-',
            ConvertInterface::LONGITUDE_DIRECTION       => $longitude['positive'] ? 'E' : 'W',
            ConvertInterface::LONGITUDE_DEGREES         => $longitude['degrees'],
            ConvertInterface::LONGITUDE_DECIMAL_MINUTES => $longitude['decimalMinutes'],
        ]);
    }

    /**
     * Alias of toDecimalMinutes function.
     *
     * @param string $format The way to format the DMS coordinate.
     *
     * @deprecated This alias is deprecated, use toDecimalMinutes()
     *
     * @return string Converted and formatted string.
     */
    public function toDM($format = ConvertInterface::DEFAULT_DM_FORMAT)
    {
        return $this->toDecimalMinutes($format);
    }

    /**
     * {@inheritDoc}
     */
    public function toDegreeDecimalMinutes($format = ConvertInterface::DEFAULT_DDM_FORMAT)
    {
        $latitude  = $this->parseCoordinate($this->coordinates->getLatitude());
        $longitude = $this->parseCoordinate($this->coordinates->getLongitude());

        $decimalPrecisionFormat = sprintf('%%0.%df', ConvertInterface::DEGREE_DECIMAL_MINUTES_PRECISION);
        $latitude['decimalMinutes']  = sprintf($decimalPrecisionFormat, $latitude['decimalMinutes']);
        $longitude['decimalMinutes'] = sprintf($decimalPrecisionFormat, $longitude['decimalMinutes']);

        return strtr($format, [
            ConvertInterface::LATITUDE_SIGN             => $latitude['positive'] ? '' : '-',
            ConvertInterface::LATITUDE_DIRECTION        => $latitude['positive'] ? 'N' : 'S',
            ConvertInterface::LATITUDE_DEGREES          => $latitude['degrees'],
            ConvertInterface::LATITUDE_DECIMAL_MINUTES  => $latitude['decimalMinutes'],
            ConvertInterface::LONGITUDE_SIGN            => $longitude['positive'] ? '' : '-',
            ConvertInterface::LONGITUDE_DIRECTION       => $longitude['positive'] ? 'E' : 'W',
            ConvertInterface::LONGITUDE_DEGREES         => $longitude['degrees'],
            ConvertInterface::LONGITUDE_DECIMAL_MINUTES => $longitude['decimalMinutes'],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function toUniversalTransverseMercator()
    {
        // Convert decimal degrees coordinates to radian.
        $phi    = deg2rad($this->coordinates->getLatitude());
        $lambda = deg2rad($this->coordinates->getLongitude());

        // Compute the zone UTM zone.
        $zone = (int) (($this->coordinates->getLongitude() + 180.0) / 6) + 1;

        // Special zone for South Norway.
        // On the southwest coast of Norway, grid zone 32V (9° of longitude in width) is extended further west,
        // and grid zone 31V (3° of longitude in width) is correspondingly shrunk to cover only open water.
        if ($this->coordinates->getLatitude() >= 56.0 && $this->coordinates->getLatitude() < 64.0
            && $this->coordinates->getLongitude() >= 3.0 && $this->coordinates->getLongitude() < 12.0) {
            $zone = 32;
        }

        // Special zone for Svalbard.
        // In the region around Svalbard, the four grid zones 31X (9° of longitude in width),
        // 33X (12° of longitude in width), 35X (12° of longitude in width), and 37X (9° of longitude in width)
        // are extended to cover what would otherwise have been covered by the seven grid zones 31X to 37X.
        // The three grid zones 32X, 34X and 36X are not used.
        if ($this->coordinates->getLatitude() >= 72.0 && $this->coordinates->getLatitude() < 84.0) {
            if ($this->coordinates->getLongitude() >= 0.0 && $this->coordinates->getLongitude() < 9.0) {
                $zone = 31;
            } elseif ($this->coordinates->getLongitude() >= 9.0 && $this->coordinates->getLongitude() < 21.0) {
                $zone = 33;
            } elseif ($this->coordinates->getLongitude() >= 21.0 && $this->coordinates->getLongitude() < 33.0) {
                $zone = 35;
            } elseif ($this->coordinates->getLongitude() >= 33.0 && $this->coordinates->getLongitude() < 42.0) {
                $zone = 37;
            }
        }

        // Determines the central meridian for the given UTM zone.
        $lambda0 = deg2rad(-183.0 + ($zone * 6.0));

        $ep2 = (pow($this->coordinates->getEllipsoid()->getA(), 2.0) -
            pow($this->coordinates->getEllipsoid()->getB(), 2.0)) / pow($this->coordinates->getEllipsoid()->getB(), 2.0);
        $nu2 = $ep2 * pow(cos($phi), 2.0);
        $nN  = pow($this->coordinates->getEllipsoid()->getA(), 2.0) /
            ($this->coordinates->getEllipsoid()->getB() * sqrt(1 + $nu2));
        $t   = tan($phi);
        $t2  = $t * $t;
        $l   = $lambda - $lambda0;

        $l3coef = 1.0 - $t2 + $nu2;
        $l4coef = 5.0 - $t2 + 9 * $nu2 + 4.0 * ($nu2 * $nu2);
        $l5coef = 5.0 - 18.0 * $t2 + ($t2 * $t2) + 14.0 * $nu2 - 58.0 * $t2 * $nu2;
        $l6coef = 61.0 - 58.0 * $t2 + ($t2 * $t2) + 270.0 * $nu2 - 330.0 * $t2 * $nu2;
        $l7coef = 61.0 - 479.0 * $t2 + 179.0 * ($t2 * $t2) - ($t2 * $t2 * $t2);
        $l8coef = 1385.0 - 3111.0 * $t2 + 543.0 * ($t2 * $t2) - ($t2 * $t2 * $t2);

        // Calculate easting.
        $easting = $nN * cos($phi) * $l
            + ($nN / 6.0 * pow(cos($phi), 3.0) * $l3coef * pow($l, 3.0))
            + ($nN / 120.0 * pow(cos($phi), 5.0) * $l5coef * pow($l, 5.0))
            + ($nN / 5040.0 * pow(cos($phi), 7.0) * $l7coef * pow($l, 7.0));

        // Calculate northing.
        $n = ($this->coordinates->getEllipsoid()->getA() - $this->coordinates->getEllipsoid()->getB()) /
            ($this->coordinates->getEllipsoid()->getA() + $this->coordinates->getEllipsoid()->getB());
        $alpha = (($this->coordinates->getEllipsoid()->getA() + $this->coordinates->getEllipsoid()->getB()) / 2.0) *
            (1.0 + (pow($n, 2.0) / 4.0) + (pow($n, 4.0) / 64.0));
        $beta = (-3.0 * $n / 2.0) + (9.0 * pow($n, 3.0) / 16.0) + (-3.0 * pow($n, 5.0) / 32.0);
        $gamma = (15.0 * pow($n, 2.0) / 16.0) + (-15.0 * pow($n, 4.0) / 32.0);
        $delta = (-35.0 * pow($n, 3.0) / 48.0) + (105.0 * pow($n, 5.0) / 256.0);
        $epsilon = (315.0 * pow($n, 4.0) / 512.0);
        $northing = $alpha
            * ($phi + ($beta * sin(2.0 * $phi))
            + ($gamma * sin(4.0 * $phi))
            + ($delta * sin(6.0 * $phi))
            + ($epsilon * sin(8.0 * $phi)))
            + ($t / 2.0 * $nN * pow(cos($phi), 2.0) * pow($l, 2.0))
            + ($t / 24.0 * $nN * pow(cos($phi), 4.0) * $l4coef * pow($l, 4.0))
            + ($t / 720.0 * $nN * pow(cos($phi), 6.0) * $l6coef * pow($l, 6.0))
            + ($t / 40320.0 * $nN * pow(cos($phi), 8.0) * $l8coef * pow($l, 8.0));

        // Adjust easting and northing for UTM system.
        $easting = $easting * GeotoolsInterface::UTM_SCALE_FACTOR + 500000.0;
        $northing = $northing * GeotoolsInterface::UTM_SCALE_FACTOR;
        if ($northing < 0.0) {
            $northing += 10000000.0;
        }

        return sprintf('%d%s %d %d',
            $zone, Geotools::$latitudeBands[(int) (($this->coordinates->getLatitude() + 80) / 8)], $easting, $northing
        );
    }

    /**
     * Alias of toUniversalTransverseMercator function.
     *
     * @deprecated This alias is deprecated, use toUniversalTransverseMercator()
     *
     * @return string The converted UTM coordinate in meters
     */
    public function toUTM()
    {
        return $this->toUniversalTransverseMercator();
    }
}
