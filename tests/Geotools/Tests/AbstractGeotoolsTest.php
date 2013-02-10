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

use Geotools\AbstractGeotools;
use Geotools\Coordinate\CoordinateInterface;

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
        $this->assertSame(6378136.047, AbstractGeotools::EARTH_RADIUS);
        $this->assertSame(1609.344, AbstractGeotools::METERS_PER_MILE);
        $this->assertSame('km', AbstractGeotools::KILOMETER_UNIT);
        $this->assertSame('mile', AbstractGeotools::MILE_UNIT);
    }

    public function testCardinalPoints()
    {
        $this->assertTrue(is_array($this->geotools->getCardinalPoints()));
        $this->assertCount(17, $this->geotools->getCardinalPoints());
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
}
