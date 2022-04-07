<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Geohash;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Exception\InvalidArgumentException;
use League\Geotools\Exception\RuntimeException;

/**
 * Geohash class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Geohash implements GeohashInterface
{
    /**
     * The minimum length of the geo hash.
     *
     * @var integer
     */
    public const MIN_LENGTH = 1;

    /**
     * The maximum length of the geo hash.
     *
     * @var integer
     */
    public const MAX_LENGTH = 12;


    /**
     * The geo hash.
     *
     * @var string
     */
    protected $geohash = '';

    /**
     * The interval of latitudes in degrees.
     *
     * @var array
     */
    protected $latitudeInterval = array(-90.0, 90.0);

    /**
     * The interval of longitudes in degrees.
     *
     * @var array
     */
    protected $longitudeInterval = array(-180.0, 180.0);

    /**
     * The interval of bits.
     *
     * @var array
     */
    protected $bits = array(16, 8, 4, 2, 1);

    /**
     * The array of chars in base 32.
     *
     * @var array
     */
    protected $base32Chars = array(
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'b', 'c', 'd', 'e', 'f', 'g',
        'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
    );


    /**
     * Returns the geo hash.
     *
     * @return string
     */
    public function getGeohash(): string
    {
        return $this->geohash;
    }

    /**
     * Returns the decoded coordinate (The center of the bounding box).
     *
     * @return CoordinateInterface
     */
    public function getCoordinate()
    {
        return new Coordinate(array(
            ($this->latitudeInterval[0] + $this->latitudeInterval[1]) / 2,
            ($this->longitudeInterval[0] + $this->longitudeInterval[1]) / 2
        ));
    }

    /**
     * Returns the bounding box which is an array of coordinates (SouthWest & NorthEast).
     *
     * @return CoordinateInterface[]
     */
    public function getBoundingBox(): array
    {
        return array(
            new Coordinate(array(
                $this->latitudeInterval[0],
                $this->longitudeInterval[0]
            )),
            new Coordinate(array(
                $this->latitudeInterval[1],
                $this->longitudeInterval[1]
            ))
        );
    }

    /**
     * {@inheritDoc}
     *
     * @see http://en.wikipedia.org/wiki/Geohash
     * @see http://geohash.org/
     */
    public function encode(CoordinateInterface $coordinate, $length = self::MAX_LENGTH): GeohashInterface
    {
        $length = (int) $length;
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new InvalidArgumentException('The length should be between 1 and 12.');
        }

        $latitudeInterval  = $this->latitudeInterval;
        $longitudeInterval = $this->longitudeInterval;
        $isEven            = true;
        $bit               = 0;
        $charIndex         = 0;

        while (strlen($this->geohash) < $length) {
            if ($isEven) {
                $middle = ($longitudeInterval[0] + $longitudeInterval[1]) / 2;
                if ($coordinate->getLongitude() > $middle) {
                    $charIndex |= $this->bits[$bit];
                    $longitudeInterval[0] = $middle;
                } else {
                    $longitudeInterval[1] = $middle;
                }
            } else {
                $middle = ($latitudeInterval[0] + $latitudeInterval[1]) / 2;
                if ($coordinate->getLatitude() > $middle) {
                    $charIndex |= $this->bits[$bit];
                    $latitudeInterval[0] = $middle;
                } else {
                    $latitudeInterval[1] = $middle;
                }
            }

            if ($bit < 4) {
                $bit++;
            } else {
                $this->geohash .= $this->base32Chars[$charIndex];
                $bit           = 0;
                $charIndex     = 0;
            }

            $isEven = !$isEven;
        }

        $this->latitudeInterval  = $latitudeInterval;
        $this->longitudeInterval = $longitudeInterval;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function decode($geohash): GeohashInterface
    {
        if (!is_string($geohash)) {
            throw new InvalidArgumentException('The geo hash should be a string.');
        }

        if (strlen($geohash) < self::MIN_LENGTH || strlen($geohash) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('The length of the geo hash should be between 1 and 12.');
        }

        $base32DecodeMap  = array();
        $base32CharsTotal = count($this->base32Chars);
        for ($i = 0; $i < $base32CharsTotal; $i++) {
            $base32DecodeMap[$this->base32Chars[$i]] = $i;
        }

        $latitudeInterval  = $this->latitudeInterval;
        $longitudeInterval = $this->longitudeInterval;
        $isEven            = true;

        $geohashLength = strlen($geohash);
        for ($i = 0; $i < $geohashLength; $i++) {

            if (!isset($base32DecodeMap[$geohash[$i]])) {
                throw new RuntimeException('This geo hash is invalid.');
            }

            $currentChar = $base32DecodeMap[$geohash[$i]];

            $bitsTotal = count($this->bits);
            for ($j = 0; $j < $bitsTotal; $j++) {
                $mask = $this->bits[$j];

                if ($isEven) {
                    if (($currentChar & $mask) !== 0) {
                        $longitudeInterval[0] = ($longitudeInterval[0] + $longitudeInterval[1]) / 2;
                    } else {
                        $longitudeInterval[1] = ($longitudeInterval[0] + $longitudeInterval[1]) / 2;
                    }
                } else {
                    if (($currentChar & $mask) !== 0) {
                        $latitudeInterval[0] = ($latitudeInterval[0] + $latitudeInterval[1]) / 2;
                    } else {
                        $latitudeInterval[1] = ($latitudeInterval[0] + $latitudeInterval[1]) / 2;
                    }
                }

                $isEven = !$isEven;
            }
        }

        $this->latitudeInterval  = $latitudeInterval;
        $this->longitudeInterval = $longitudeInterval;

        return $this;
    }
}
