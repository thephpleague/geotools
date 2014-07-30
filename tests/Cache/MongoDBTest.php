<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Cache;

use League\Geotools\Tests\TestCase;
use League\Geotools\Cache\MongoDB;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class MongoDBTest extends TestCase
{
    protected $mongo;

    protected function setUp()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('You need to install Mongo.');
        }

        $this->mongo = new TestableMongoDB();
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage Failed to connect to: foo:27017: php_network_getaddresses: getaddrinfo failed: Name or service not known
     */
    public function testConstructorThrowsInvalidArgumentException()
    {
        new MongoDB('foo', 'bar', 'baz');
    }

    public function testConstructor()
    {
        try {
            new MongoDB();
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    public function testGetKey()
    {
        $key = $this->mongo->getKey('foo', 'bar');

        $this->assertTrue(is_string($key));
        $this->assertEquals('3858f62230ac3c915f300c664312c63f', $key);
    }

    /**
     * @expectedException League\Geotools\Exception\RuntimeException
     * @expectedExceptionMessage boo
     */
    public function testCacheThrowsRuntimeException()
    {
        try {
            $mockMongo = $this->getMock('\Mongo', array('insert'));
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $mockMongo
            ->expects($this->once())
            ->method('insert')
            ->will($this->throwException(new \Exception('boo')));

        $this->mongo->setCollection($mockMongo);
        $this->mongo->cache($this->getMock('\League\Geotools\Batch\BatchGeocoded'));
    }

    public function testCache()
    {
        try {
            $mockMongo = $this->getMock('\Mongo', array('insert'));
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $mockMongo
            ->expects($this->once())
            ->method('insert');

        $this->mongo->setCollection($mockMongo);
        $this->mongo->cache($this->getMock('\League\Geotools\Batch\BatchGeocoded'));
    }

    public function testIsCachedReturnsFalse()
    {
        try {
            $mockMongo = $this->getMock('\Mongo', array('findOne'));
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $mockMongo
            ->expects($this->once())
            ->method('findOne')
            ->will($this->returnValue(null));

        $this->mongo->setCollection($mockMongo);
        $cached = $this->mongo->isCached('foo', 'bar');

        $this->assertFalse($cached);
    }

    public function testIsCachedReturnsBatchGeocodedObject()
    {
        $array = array(
            'id'               => 'f2dcb93aa43e3ad731d54143d2a4a373',
            'providerName'     => 'google_maps',
            'query'            => 'Paris, France',
            'exceptionMessage' => '',
            'coordinates'      =>
                array (
                    0 => 48.856614,
                    1 => 2.3522219,
                ),
            'latitude'  => 48.856614,
            'longitude' => 2.3522219,
            'bounds' =>
                array (
                    'south' => 48.815573,
                    'west'  => 2.224199,
                    'north' => 48.9021449,
                    'east'  => 2.4699208,
                ),
            'streetNumber' => null,
            'streetName'   => null,
            'city'         => 'Paris',
            'zipcode'      => null,
            'cityDistrict' => null,
            'county'       => 'Paris',
            'countyCode'   => '75',
            'region'       => 'Île-De-France',
            'regionCode'   => 'IDF',
            'country'      => 'France',
            'countryCode'  => 'FR',
            'timezone'     => null,
        );

        try {
            $mockMongo = $this->getMock('\Mongo', array('findOne'));
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $mockMongo
            ->expects($this->once())
            ->method('findOne')
            ->will($this->returnValue($array));

        $this->mongo->setCollection($mockMongo);
        $cached = $this->mongo->isCached('foo', 'bar');

        $this->assertTrue(is_object($cached));
        $this->assertInstanceOf('\League\Geotools\Batch\BatchGeocoded', $cached);
        $this->assertEquals('Google_Maps', $cached->getProviderName());
        $this->assertEquals('Paris, France', $cached->getQuery());
        $this->assertEmpty($cached->getExceptionMessage());
        $this->assertTrue(is_array($cached->getCoordinates()));
        $this->assertCount(2, $cached->getCoordinates());
        $this->assertEquals(48.856614, $cached->getLatitude());
        $this->assertEquals(2.3522219, $cached->getLongitude());
        $bounds = $cached->getBounds();
        $this->assertTrue(is_array($bounds));
        $this->assertCount(4, $bounds);
        $this->assertEquals(48.815573, $bounds['south']);
        $this->assertEquals(2.224199, $bounds['west']);
        $this->assertEquals(48.9021449, $bounds['north']);
        $this->assertEquals(2.4699208, $bounds['east']);
        $this->assertNull($cached->getStreetNumber());
        $this->assertNull($cached->getStreetName());
        $this->assertEquals('Paris', $cached->getCity());
        $this->assertNull($cached->getZipCode());
        $this->assertNull($cached->getCityDistrict());
        $this->assertEquals('Paris', $cached->getCounty());
        $this->assertEquals(75, $cached->getCountyCode());
        $this->assertEquals('Île-De-France', $cached->getRegion());
        $this->assertEquals('IDF', $cached->getRegionCode());
        $this->assertEquals('France', $cached->getCountry());
        $this->assertEquals('FR', $cached->getCountryCode());
        $this->assertNull($cached->getTimezone());
    }

    public function testFlush()
    {
        try {
            $mockMongo = $this->getMock('\Mongo', array('drop'));
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $mockMongo
            ->expects($this->once())
            ->method('drop');

        $this->mongo->setCollection($mockMongo);
        $cached = $this->mongo->flush();
    }
}

class TestableMongoDB extends MongoDB
{
    public function __construct()
    {
        //
    }

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }
}
