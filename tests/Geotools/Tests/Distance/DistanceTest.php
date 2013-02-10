<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests\Distance;

use Geotools\Tests\TestCase;
use Geotools\Distance\Distance;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class DistanceTest extends TestCase
{
    protected $distance;
    protected $from;
    protected $to;
    protected $coordA;
    protected $coordB;

    protected function setUp()
    {
        $this->distance = new TestableDistance();
        $this->from     = $this->getStubCoordinate();
        $this->to       = $this->getStubCoordinate();
        $this->coordA   = array(48.8234055, 2.3072664);
        $this->coordB   = array(43.296482, 5.36978);
    }

    public function testSetFromValueShouldBeACoordinateInterface()
    {
        $this->distance->setFrom($this->from);
        $from = $this->distance->getFrom();

        $this->assertTrue(is_object($from));
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $from);
    }

    public function testSetFromShouldReturnTheSameDistanceInstance()
    {
        $distance = $this->distance->setFrom($this->from);

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('Geotools\Distance\DistanceInterface', $distance);
        $this->assertSame($this->distance, $distance);
    }

    public function testSetToValueShouldBeACoordinateInterface()
    {
        $this->distance->setTo($this->to);
        $to = $this->distance->getTo();

        $this->assertTrue(is_object($to));
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $to);
    }

    public function testSetToShouldReturnTheSameDistanceInstance()
    {
        $distance = $this->distance->setTo($this->to);

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('Geotools\Distance\DistanceInterface', $distance);
        $this->assertSame($this->distance, $distance);
    }

    public function testIn()
    {
        $distance = $this->distance->in('foo');

        $this->assertSame('foo', $distance->getIn());
    }

    public function testInShouldReturnTheSameDistanceInstance()
    {
        $distance = $this->distance->in('foo');

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('Geotools\Distance\DistanceInterface', $distance);
        $this->assertSame($this->distance, $distance);
    }

    public function testFlatDistance()
    {
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(659166.50524477, $this->distance->flat(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(659.16650524477, $this->distance->in('km')->flat(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(409.58707724686, $this->distance->in('mile')->flat(), '', 0.00001);
    }

    public function testHaversineDistance()
    {
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(659021.91298475, $this->distance->haversine(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(659.02191298475, $this->distance->in('km')->haversine(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(409.49723178186, $this->distance->in('mile')->haversine(), '', 0.00001);
    }

    public function testVincentyDistance()
    {
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(658307.53717626, $this->distance->vincenty(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(658.30753717626, $this->distance->in('km')->vincenty(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB));
        $this->assertEquals(409.05333923404, $this->distance->in('mile')->vincenty(), '', 0.00001);
    }
}

class TestableDistance extends Distance
{
    public function getIn()
    {
        return $this->unit;
    }
}
