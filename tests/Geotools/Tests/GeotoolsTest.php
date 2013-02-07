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
use Geotools\Coordinate\Coordinate;
use Geocoder\Geocoder;

/**
 * Geotools test class
 *
* @author Antoine Corcy <contact@sbin.dk>
*/
class GeotoolsTest extends TestCase
{
    protected $geotools;
    protected $from;
    protected $to;

    protected function setUp()
    {
        $this->geotools = new TestableGeotools();
        $this->from = new MockCoordinate();
        $this->to = new MockCoordinate();
    }

    public function testFromValueShouldBeACoordinateInterface()
    {
        $this->geotools->from($this->from);
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $this->geotools->getFrom());
    }

    public function testFromShouldReturnTheSameGeotoolsInstance()
    {
        $this->assertSame($this->geotools, $this->geotools->from($this->from));
    }

    public function testToValueShouldBeACoordinateInterface()
    {
        $this->geotools->to($this->to);
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $this->geotools->getTo());
    }

    public function testToShouldReturnTheSameGeotoolsInstance()
    {
        $this->assertSame($this->geotools, $this->geotools->to($this->to));
    }

    public function testDistanceShouldReturnANewDistanceInstance()
    {
        $this->geotools->from($this->from);
        $this->geotools->to($this->to);
        $this->assertInstanceOf('Geotools\Distance\DistanceInterface', $this->geotools->distance());
    }

    public function testPointShouldReturnANewPointInstance()
    {
        $this->geotools->from($this->from);
        $this->geotools->to($this->to);
        $this->assertInstanceOf('Geotools\Point\PointInterface', $this->geotools->point());
    }

    public function testBatchShouldReturnANewBatchInstance()
    {
        $geocoder = new Geocoder();
        $this->assertInstanceOf('Geotools\Batch\BatchInterface', $this->geotools->batch($geocoder));
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

class MockCoordinate extends Coordinate
{
    public function __construct()
    {
    }
}
