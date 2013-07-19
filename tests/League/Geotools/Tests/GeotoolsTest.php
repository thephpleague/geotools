<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests;

use League\Geotools\Geotools;
use Geocoder\Geocoder;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class GeotoolsTest extends TestCase
{
    protected $geotools;

    protected function setUp()
    {
        $this->geotools = new Geotools();
    }

    public function testDistanceShouldReturnANewDistanceInstance()
    {
        $distance = $this->geotools->distance();

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('League\Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('League\Geotools\Distance\DistanceInterface', $distance);
    }

    public function testPointShouldReturnANewPointInstance()
    {
        $point = $this->geotools->point();

        $this->assertTrue(is_object($point));
        $this->assertInstanceOf('League\Geotools\Point\Point', $point);
        $this->assertInstanceOf('League\Geotools\Point\PointInterface', $point);
    }

    public function testBatchShouldReturnANewBatchInstance()
    {
        $geocoder = new Geocoder();
        $batch = $this->geotools->batch($geocoder);

        $this->assertTrue(is_object($batch));
        $this->assertInstanceOf('League\Geotools\Batch\Batch', $batch);
        $this->assertInstanceOf('League\Geotools\Batch\BatchInterface', $batch);
    }

    public function testGeohashShouldReturnANewGeohashInstance()
    {
        $geohash = $this->geotools->geohash();

        $this->assertTrue(is_object($geohash));
        $this->assertInstanceOf('League\Geotools\Geohash\Geohash', $geohash);
        $this->assertInstanceOf('League\Geotools\Geohash\GeohashInterface', $geohash);
    }

    public function testConvertShouldReturnsANewConvertInstance()
    {
        $convert = $this->geotools->convert($this->getStubCoordinate());

        $this->assertTrue(is_object($convert));
        $this->assertInstanceOf('League\Geotools\Convert\Convert', $convert);
        $this->assertInstanceOf('League\Geotools\Convert\ConvertInterface', $convert);
    }
}
