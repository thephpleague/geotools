<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests;

use Geotools\Geotools;
use Geocoder\Geocoder;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class GeotoolsTest extends TestCase
{
    protected $geotools;
    protected $from;
    protected $to;
    protected $coordinates;

    protected function setUp()
    {
        $this->geotools    = new TestableGeotools();
        $this->from        = $this->getStubCoordinate();
        $this->to          = $this->getStubCoordinate();
        $this->coordinates = $this->getStubCoordinate();
    }

    public function testFromValueShouldBeACoordinateInterface()
    {
        $this->geotools->from($this->from);
        $from = $this->geotools->getFrom();

        $this->assertTrue(is_object($from));
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $from);
    }

    public function testFromShouldReturnTheSameGeotoolsInstance()
    {
        $geotools = $this->geotools->from($this->from);

        $this->assertTrue(is_object($geotools));
        $this->assertInstanceOf('Geotools\Geotools', $geotools);
        $this->assertInstanceOf('Geotools\GeotoolsInterface', $geotools);
        $this->assertSame($this->geotools, $geotools);
    }

    public function testToValueShouldBeACoordinateInterface()
    {
        $this->geotools->to($this->to);
        $to = $this->geotools->getTo();

        $this->assertTrue(is_object($to));
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $to);
    }

    public function testToShouldReturnTheSameGeotoolsInstance()
    {
        $geotools = $this->geotools->to($this->to);

        $this->assertTrue(is_object($geotools));
        $this->assertInstanceOf('Geotools\Geotools', $geotools);
        $this->assertInstanceOf('Geotools\GeotoolsInterface', $geotools);
        $this->assertSame($this->geotools, $geotools);
    }

    public function testDistanceShouldReturnANewDistanceInstance()
    {
        $this->geotools->from($this->from);
        $this->geotools->to($this->to);
        $distance = $this->geotools->distance();

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('Geotools\Distance\DistanceInterface', $distance);
    }

    public function testPointShouldReturnANewPointInstance()
    {
        $this->geotools->from($this->from);
        $this->geotools->to($this->to);
        $point = $this->geotools->point();

        $this->assertTrue(is_object($point));
        $this->assertInstanceOf('Geotools\Point\Point', $point);
        $this->assertInstanceOf('Geotools\Point\PointInterface', $point);
    }

    public function testBatchShouldReturnANewBatchInstance()
    {
        $geocoder = new Geocoder();
        $batch = $this->geotools->batch($geocoder);

        $this->assertTrue(is_object($batch));
        $this->assertInstanceOf('Geotools\Batch\Batch', $batch);
        $this->assertInstanceOf('Geotools\Batch\BatchInterface', $batch);
    }

    public function testGeohashShouldReturnANewGeohashInstance()
    {
        $geohash = $this->geotools->geohash();

        $this->assertTrue(is_object($geohash));
        $this->assertInstanceOf('Geotools\Geohash\Geohash', $geohash);
        $this->assertInstanceOf('Geotools\Geohash\GeohashInterface', $geohash);
    }

    public function testConvertShouldReturnsANewConvertInstance()
    {
        $convert = $this->geotools->convert($this->coordinates);

        $this->assertTrue(is_object($convert));
        $this->assertInstanceOf('Geotools\Convert\Convert', $convert);
        $this->assertInstanceOf('Geotools\Convert\ConvertInterface', $convert);
    }
}

class TestableGeotools extends Geotools
{
    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }
}
