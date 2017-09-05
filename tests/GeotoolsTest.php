<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests;

use League\Geotools\Geotools;
use Geocoder\ProviderAggregator as Geocoder;
use League\Geotools\GeotoolsInterface;

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

    public function testConsts()
    {
        $this->assertSame(0.9996, GeotoolsInterface::UTM_SCALE_FACTOR);
        $this->assertSame(1609.344, GeotoolsInterface::METERS_PER_MILE);
        $this->assertSame(0.3048, GeotoolsInterface::FEET_PER_METER);
        $this->assertSame('km', GeotoolsInterface::KILOMETER_UNIT);
        $this->assertSame('mi', GeotoolsInterface::MILE_UNIT);
        $this->assertSame('ft', GeotoolsInterface::FOOT_UNIT);
    }

    public function testCardinalPoints()
    {
        $this->assertTrue(is_array(Geotools::$cardinalPoints));
        $this->assertCount(17, Geotools::$cardinalPoints);
    }

    public function testLatitudeBands()
    {
        $this->assertTrue(is_array(Geotools::$latitudeBands));
        $this->assertCount(21, Geotools::$latitudeBands);
    }

    public function testDistanceShouldReturnANewDistanceInstance()
    {
        $distance = $this->geotools->distance();

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('League\Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('League\Geotools\Distance\DistanceInterface', $distance);
    }

    public function testVertexShouldReturnANewVertexInstance()
    {
        $vertex = $this->geotools->vertex();

        $this->assertTrue(is_object($vertex));
        $this->assertInstanceOf('League\Geotools\Vertex\Vertex', $vertex);
        $this->assertInstanceOf('League\Geotools\Vertex\VertexInterface', $vertex);
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
