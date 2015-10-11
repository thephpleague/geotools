<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Geohash;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geohash\TenTen;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class TenTenTest extends \League\Geotools\Tests\TestCase
{
    public function testFoo()
    {
        $tenten = new TenTen;

        $this->assertSame('MEQ N6G 7NY5', $tenten->encode(new Coordinate([51.09559, 1.12207])));
        $this->assertSame('MEH YHM 0QPR', $tenten->encode(new Coordinate([51, 1])));
        $this->assertSame('K93 7JE QY7Y', $tenten->encode(new Coordinate([48.856614, 2.3522219])));
    }
}
