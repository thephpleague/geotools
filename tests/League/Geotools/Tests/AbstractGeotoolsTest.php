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

use League\Geotools\AbstractGeotools;
use League\Geotools\Coordinate\CoordinateInterface;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class AbstractGeotoolsTest extends TestCase
{
    protected $geotools;

    protected function setUp()
    {
        $this->geotools = new MockGeotools();
    }

    public function testConsts()
    {
        $this->assertSame(0.9996, AbstractGeotools::UTM_SCALE_FACTOR);
        $this->assertSame(1609.344, AbstractGeotools::METERS_PER_MILE);
        $this->assertSame(0.3048, AbstractGeotools::FEET_PER_METER);
        $this->assertSame('km', AbstractGeotools::KILOMETER_UNIT);
        $this->assertSame('mi', AbstractGeotools::MILE_UNIT);
        $this->assertSame('ft', AbstractGeotools::FOOT_UNIT);
    }

    public function testCardinalPoints()
    {
        $this->assertTrue(is_array($this->geotools->getCardinalPoints()));
        $this->assertCount(17, $this->geotools->getCardinalPoints());
    }

    public function testLatitudeBands()
    {
        $this->assertTrue(is_array($this->geotools->getLatitudeBands()));
        $this->assertCount(21, $this->geotools->getLatitudeBands());
    }

    public function testFrom()
    {
        $this->assertNull($this->geotools->getFrom());
    }

    public function testTo()
    {
        $this->assertNull($this->geotools->getTo());
    }
}

class MockGeotools extends AbstractGeotools
{
    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getCardinalPoints()
    {
        return $this->cardinalPoints;
    }

    public function getLatitudeBands()
    {
        return $this->latitudeBands;
    }
}
