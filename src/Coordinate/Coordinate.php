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

use Geocoder\Location;
use League\Geotools\Exception\InvalidArgumentException;

/**
 * Coordinate class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Coordinate implements CoordinateInterface, \JsonSerializable
{
    /**
     * The latitude of the coordinate.
     *
     * @var double
     */
    protected $latitude;

    /**
     * The longitude of the coordinate.
     *
     * @var double
     */
    protected $longitude;

    /**
     * The selected ellipsoid.
     *
    * @var Ellipsoid
    */
    protected $ellipsoid;


    /**
     * The precision to use to compare big numbers
     *
     * @var integer
     */
    private $precision = 8;

    /**
     * Set the latitude and the longitude of the coordinates into an selected ellipsoid.
     *
     * @param Location|array|string         $coordinates The coordinates.
     * @param Ellipsoid|null                $ellipsoid   The selected ellipsoid (WGS84 by default).
     *
     * @throws InvalidArgumentException
     */
    public function __construct($coordinates, ?Ellipsoid $ellipsoid = null)
    {
        if ($coordinates instanceof Location) {
            if (null !== $locationCoordinates = $coordinates->getCoordinates()) {
                $this->setLatitude($locationCoordinates->getLatitude());
                $this->setLongitude($locationCoordinates->getLongitude());
            }
        } elseif (is_array($coordinates) && 2 === count($coordinates)) {
            $this->setLatitude($coordinates[0]);
            $this->setLongitude($coordinates[1]);
        } elseif (is_string($coordinates)) {
            $this->setFromString($coordinates);
        } else {
            throw new InvalidArgumentException(
                'It should be a string, an array or a class which implements Geocoder\Model\Address !'
            );
        }

        $this->ellipsoid = $ellipsoid ?: Ellipsoid::createFromName(Ellipsoid::WGS84);
    }

    /**
     * {@inheritDoc}
     */
    public function normalizeLatitude($latitude)
    {
        $latitude = rtrim(sprintf('%.13F', max(-90, min(90, $latitude))), 0);

        return '.' === substr($latitude, -1) ? $latitude . '0' : $latitude;
    }

    /**
     * {@inheritDoc}
     */
    public function normalizeLongitude($longitude)
    {
        if (180 === floor($longitude) % 360) {
            return '180.0';
        }

        $mod       = fmod($longitude, 360);
        $longitude = $mod < -180 ? $mod + 360 : ($mod > 180 ? $mod - 360 : $mod);
        $longitude = rtrim(sprintf('%.13F', $longitude), 0);

        return '.' === substr($longitude, -1) ? $longitude . '0' : $longitude;
    }

    /**
     * {@inheritDoc}
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $this->normalizeLatitude($latitude);
    }

    /**
     * {@inheritDoc}
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * {@inheritDoc}
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $this->normalizeLongitude($longitude);
    }

    /**
     * {@inheritDoc}
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * {@inheritDoc}
     */
    public function getEllipsoid()
    {
        return $this->ellipsoid;
    }

    /**
     * Creates a valid and acceptable geographic coordinates.
     *
     * @param string $coordinates
     *
     * @throws InvalidArgumentException
     */
    public function setFromString($coordinates)
    {
        if (!is_string($coordinates)) {
            throw new InvalidArgumentException('The given coordinates should be a string !');
        }

        try {
            $inDecimalDegree = $this->toDecimalDegrees($coordinates);
            $this->setLatitude($inDecimalDegree[0]);
            $this->setLongitude($inDecimalDegree[1]);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * @return integer
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param  integer $precision
     * @return $this
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }


    /**
     * Converts a valid and acceptable geographic coordinates to decimal degrees coordinate.
     *
     * @param string $coordinates A valid and acceptable geographic coordinates.
     *
     * @return array An array of coordinate in decimal degree.
     *
     * @throws InvalidArgumentException
     *
     * @see http://en.wikipedia.org/wiki/Geographic_coordinate_conversion
     */
    private function toDecimalDegrees($coordinates)
    {
        // Degrees, Decimal Minutes format (DD MM.mmm)
        // N 40°26.7717 E 79°56.93172
        // N40°26.7717E79°56.93172
        // N 25°59.86′, W 21°09.81′
        if (preg_match('/([ns]{1})\s?([0-9]{1,2})\D+([0-9]{1,2}\.?\d*)\D*[, ]?([we]{1})\s? ?([0-9]{1,3})\D+([0-9]{1,2}\.?\d*)\D*$/i', $coordinates, $match)) {
            $latitude  = $match[2] + $match[3] / 60;
            $longitude = $match[5] + $match[6] / 60;

            return [
                'N' === strtoupper($match[1]) ? $latitude : -$latitude,
                'E' === strtoupper($match[4]) ? $longitude : -$longitude,
            ];
        }

        // 40.446195, -79.948862
        if (preg_match('/(\-?[0-9]{1,2}\.?\d*)[, ] ?(\-?[0-9]{1,3}\.?\d*)$/', $coordinates, $match)) {
            return array($match[1], $match[2]);
        }

        // 40° 26.7717, -79° 56.93172
        if (preg_match('/(\-?[0-9]{1,2})\D+([0-9]{1,2}\.?\d*)[, ] ?(\-?[0-9]{1,3})\D+([0-9]{1,2}\.?\d*)$/i',
            $coordinates, $match)) {
            return array(
                $match[1] < 0
                    ? $match[1] - $match[2] / 60
                    : $match[1] + $match[2] / 60,
                $match[3] < 0
                    ? $match[3] - $match[4] / 60
                    : $match[3] + $match[4] / 60
            );
        }

        // 40.446195N 79.948862W
        if (preg_match('/([0-9]{1,2}\.?\d*)\D*([ns]{1})[, ] ?([0-9]{1,3}\.?\d*)\D*([we]{1})$/i', $coordinates, $match)) {
            return array(
                'N' === strtoupper($match[2]) ? $match[1] : -$match[1],
                'E' === strtoupper($match[4]) ? $match[3] : -$match[3]
            );
        }

        // 40°26.7717S 79°56.93172E
        // 25°59.86′N,21°09.81′W
        if (preg_match('/([0-9]{1,2})\D+([0-9]{1,2}\.?\d*)\D*([ns]{1})[, ] ?([0-9]{1,3})\D+([0-9]{1,2}\.?\d*)\D*([we]{1})$/i',
            $coordinates, $match)) {
            $latitude  = $match[1] + $match[2] / 60;
            $longitude = $match[4] + $match[5] / 60;

            return array(
                'N' === strtoupper($match[3]) ? $latitude  : -$latitude,
                'E' === strtoupper($match[6]) ? $longitude : -$longitude
            );
        }

        // 40:26:46N, 079:56:55W
        // 40:26:46.302N 079:56:55.903W
        // 40°26′47″N 079°58′36″W
        // 40d 26′ 47″ N 079d 58′ 36″ W
        if (preg_match('/([0-9]{1,2})\D+([0-9]{1,2})\D+([0-9]{1,2}\.?\d*)\D*([ns]{1})[, ] ?([0-9]{1,3})\D+([0-9]{1,2})\D+([0-9]{1,2}\.?\d*)\D*([we]{1})$/i',
            $coordinates, $match)) {
            $latitude  = $match[1] + ($match[2] * 60 + $match[3]) / 3600;
            $longitude = $match[5] + ($match[6] * 60 + $match[7]) / 3600;
            return array(
                'N' === strtoupper($match[4]) ? $latitude  : -$latitude,
                'E' === strtoupper($match[8]) ? $longitude : -$longitude
            );
        }

        throw new InvalidArgumentException(
            'It should be a valid and acceptable ways to write geographic coordinates !'
        );
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [$this->latitude, $this->longitude];
    }

    /**
     * Returns a boolean determining coordinates equality
     * @param  Coordinate  $coordinate
     * @return boolean
     */
    public function isEqual(Coordinate $coordinate) {
        return bccomp($this->latitude, $coordinate->getLatitude(), $this->getPrecision()) === 0 && bccomp($this->longitude, $coordinate->getLongitude(), $this->getPrecision()) === 0;
    }
}
