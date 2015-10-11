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

use League\Geotools\Coordinate\CoordinateInterface;

/**
 * TenTen class
 *
 * @see http://blog.jgc.org/2006/07/simple-code-for-entering-latitude-and.html
 * @see http://blog.jgc.org/2010/06/1010-code.html
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class TenTen
{
    /**
     * The alphabet base.
     *
     * @var integer
     */
    const BASE = 29;

    /**
     * The used alphabet.
     *
     * @var string
     */
    private $alphabet = 'ABCDEFGHJKMNPQRVWXY0123456789';

    /**
     * Encode the coordinate via the 10:10 algorithm.
     *
     * @param  CoordinateInterface $coordinate The coordinate to encode.
     * @return string
     */
    public function encode(CoordinateInterface $coordinate)
    {
        $latitude  = floor(($coordinate->getLatitude() + 90.0) * 10000.0);
        $longitude = floor(($coordinate->getLongitude() +  180.0) * 10000.0);

        $position   = $latitude * 3600000.0 + $longitude;
        $ttNumber   = $position * self::BASE;
        $checkDigit = 0;

        for ($i = 1; $i < 10; ++$i) {
            $checkDigit += ($position % self::BASE) * $i;
            $position = floor($position / self::BASE);
        }

        $checkDigit %= self::BASE;

        $ttNumber += $checkDigit;
        $ttNumber = floor($ttNumber);

        $tt = '';
        for ($i = 0; $i < 10; ++$i) {
            $digit = $ttNumber % self::BASE;
            if ($i === 4 || $i === 7) {
                $tt = ' ' . $tt;
            }
            $tt = $this->alphabet[$digit] . $tt;

            $ttNumber = floor($ttNumber / self::BASE);
        }

        return $tt;
    }
}
