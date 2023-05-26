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

    public const DIRECTION_NORTH = 'north';
    public const DIRECTION_SOUTH = 'south';
    public const DIRECTION_WEST = 'west';
    public const DIRECTION_EAST = 'east';

    public const DIRECTION_NORTH_WEST = 'north_west';
    public const DIRECTION_NORTH_EAST = 'north_east';
    public const DIRECTION_SOUTH_WEST = 'south_west';
    public const DIRECTION_SOUTH_EAST = 'south_east';

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

    private $neighbors = array(
        'north' => array(
            'even' => 'p0r21436x8zb9dcf5h7kjnmqesgutwvy',
            'odd'  => 'bc01fg45238967deuvhjyznpkmstqrwx',
        ),
        'south' => array(
            'even' => '14365h7k9dcfesgujnmqp0r2twvyx8zb',
            'odd'  => '238967debc01fg45kmstqrwxuvhjyznp',
        ),
        'west' => array(
            'even' => '238967debc01fg45kmstqrwxuvhjyznp',
            'odd'  => '14365h7k9dcfesgujnmqp0r2twvyx8zb',
        ),
        'east' => array(
            'even' => 'bc01fg45238967deuvhjyznpkmstqrwx',
            'odd'  => 'p0r21436x8zb9dcf5h7kjnmqesgutwvy',
        ),
    );

    private $borders = array(
        'north' => array(
            'even' => 'prxz',
            'odd'  => 'bcfguvyz',
        ),
        'south' => array(
            'even' => '028b',
            'odd'  => '0145hjnp',
        ),
        'west' => array(
            'even' => '0145hjnp',
            'odd'  => '028b',
        ),
        'east' => array(
            'even' => 'bcfguvyz',
            'odd'  => 'prxz',
        ),
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
     * Returns the code of the adjacent area
     *
     * @param  string  $direction
     *
     * @return string
     */
    public function getNeighbor(string $direction): string
    {
        $geohash = $this->getGeohash();

        if (in_array($direction, [self::DIRECTION_NORTH_WEST, self::DIRECTION_NORTH_EAST])) {
            $geohash = $this->calculateAdjacent($geohash, self::DIRECTION_NORTH);
        }

        if (in_array($direction, [self::DIRECTION_SOUTH_WEST, self::DIRECTION_SOUTH_EAST])) {
            $geohash = $this->calculateAdjacent($geohash, self::DIRECTION_SOUTH);
        }

        if (in_array($direction, [self::DIRECTION_NORTH_WEST, self::DIRECTION_SOUTH_WEST])) {
            $direction = self::DIRECTION_WEST;
        }

        if (in_array($direction, [self::DIRECTION_NORTH_EAST, self::DIRECTION_SOUTH_EAST])) {
            $direction = self::DIRECTION_EAST;
        }

        return $this->calculateAdjacent($geohash, $direction);
    }

    /**
     * Returns neighboring area codes
     *
     * @param  bool  $includingCornerNeighbors
     *
     * @return array
     */
    public function getNeighbors(bool $includingCornerNeighbors = false): array
    {
        $geohash = $this->getGeohash();

        $north = $this->calculateAdjacent($geohash, self::DIRECTION_NORTH);
        $south = $this->calculateAdjacent($geohash, self::DIRECTION_SOUTH);
        $west = $this->calculateAdjacent($geohash, self::DIRECTION_WEST);
        $east = $this->calculateAdjacent($geohash, self::DIRECTION_EAST);

        $neighbors = array(
            self::DIRECTION_NORTH => $north,
            self::DIRECTION_SOUTH => $south,
            self::DIRECTION_WEST  => $west,
            self::DIRECTION_EAST  => $east,
        );

        if ($includingCornerNeighbors) {
            $neighbors = array_merge($neighbors, array(
                self::DIRECTION_NORTH_WEST => $this->calculateAdjacent($north, self::DIRECTION_WEST),
                self::DIRECTION_NORTH_EAST => $this->calculateAdjacent($north, self::DIRECTION_EAST),
                self::DIRECTION_SOUTH_WEST => $this->calculateAdjacent($south, self::DIRECTION_WEST),
                self::DIRECTION_SOUTH_EAST => $this->calculateAdjacent($south, self::DIRECTION_EAST),
            ));
        }

        return $neighbors;
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

        $this->geohash = $geohash;

        $this->latitudeInterval  = $latitudeInterval;
        $this->longitudeInterval = $longitudeInterval;

        return $this;
    }

    protected function calculateAdjacent(string $geohash, string $direction): string
    {
        $geohash = strtolower($geohash);
        $lastChr = $geohash[strlen($geohash) - 1];
        $type = (strlen($geohash) % 2) ? 'odd' : 'even';
        $base = substr($geohash, 0, strlen($geohash) - 1);

        if (!empty($base) && strpos($this->borders[$direction][$type], $lastChr) !== false) {
            $base = $this->calculateAdjacent($base, $direction);
        }

        return $base.$this->base32Chars[strpos($this->neighbors[$direction][$type], $lastChr)];
    }
}
