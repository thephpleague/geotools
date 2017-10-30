<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Cache;

use League\Geotools\Cache\Redis;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class RedisTest extends \League\Geotools\Tests\TestCase
{
    protected $redis;

    protected function setUp()
    {
        if (!class_exists('Predis\\Client')) {
            $this->markTestSkipped('You need to install Predis.');
        }

        $this->redis = new TestableRedis;
    }

    public function testConstructorDoesNotThrowInvalidArgumentException()
    {
        new Redis(array('foo.bar'));
    }

    public function testGetKey()
    {
        $key = $this->redis->getKey('foo', 'bar');

        $this->assertTrue(is_string($key));
        $this->assertEquals('3858f62230ac3c915f300c664312c63f', $key);
    }

    public function testCache()
    {
        $mockRedis = $this->getMockBuilder('\Predis\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('set', 'expire'))
            ->getMock();

        $mockRedis
            ->expects($this->once())
            ->method('set');
        $mockRedis
            ->expects($this->once())
            ->method('expire');

        $this->redis->setRedis($mockRedis);

        $mockGeocoded = $this->createMock('\League\Geotools\Batch\BatchGeocoded');
        $mockGeocoded
            ->expects($this->atLeastOnce())
            ->method('getProviderName');
        $mockGeocoded
            ->expects($this->atLeastOnce())
            ->method('getQuery');

        $this->redis->setExpire(12345);
        $this->redis->cache($mockGeocoded);
    }

    public function testIsCachedReturnsFalse()
    {
        $mockRedis = $this->getMockBuilder('\Predis\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('exists'))
            ->getMock();

        $mockRedis
            ->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(false));

        $this->redis->setRedis($mockRedis);
        $cached = $this->redis->isCached('foo', 'bar');

        $this->assertFalse($cached);
    }

    public function testIsCachedReturnsBatchGeocodedObject()
    {
        $json = <<<JSON
{
    "providerName": "google_maps",
    "query": "Paris, France",
    "exceptionMessage": "",
    "coordinates": [48.856614, 2.3522219],
    "latitude": 48.856614,
    "longitude": 2.3522219,
    "address": {
        "latitude": 48.856614,
        "longitude": 2.3522219,
        "bounds": {
            "south": 48.815573,
            "west": 2.224199,
            "north": 48.9021449,
            "east": 2.4699208
        },
        "streetNumber": null,
        "streetName": null,
        "locality": "Paris",
        "postalCode": null,
        "subLocality": null,
        "adminLevels": {
            "1": {
                "level": 1,
                "name": "New York",
                "code": "NY"
            },
            "2": {
                "level": 2,
                "name": "New York County",
                "code": "New York County"
            }
        },
        "country": "France",
        "countryCode": "FR",
        "timezone": null
    }
}
JSON
        ;

        $mockRedis = $this->getMockBuilder('\Predis\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('exists', 'get'))
            ->getMock();

        $mockRedis
            ->expects($this->atLeastOnce())
            ->method('exists')
            ->will($this->returnValue(true));
        $mockRedis
            ->expects($this->atLeastOnce())
            ->method('get')
            ->will($this->returnValue($json));

        $this->redis->setRedis($mockRedis);
        $cached = $this->redis->isCached('foo', 'bar');

        $this->assertTrue(is_object($cached));
        $this->assertInstanceOf('\League\Geotools\Batch\BatchGeocoded', $cached);
        $this->assertEquals('google_maps', $cached->getProviderName());
        $this->assertEquals('Paris, France', $cached->getQuery());
        $this->assertEmpty($cached->getExceptionMessage());
        $this->assertInstanceOf('\Geocoder\Model\Coordinates', $cached->getCoordinates());
        $this->assertEquals(48.856614, $cached->getLatitude());
        $this->assertEquals(2.3522219, $cached->getLongitude());
        $this->assertInstanceOf('\Geocoder\Model\Bounds', $cached->getBounds());
        $bounds = $cached->getBounds()->toArray();
        $this->assertTrue(is_array($bounds));
        $this->assertCount(4, $bounds);
        $this->assertEquals(48.815573, $bounds['south']);
        $this->assertEquals(2.224199, $bounds['west']);
        $this->assertEquals(48.9021449, $bounds['north']);
        $this->assertEquals(2.4699208, $bounds['east']);
        $this->assertNull($cached->getStreetNumber());
        $this->assertNull($cached->getStreetName());
        $this->assertEquals('Paris', $cached->getLocality());
        $this->assertNull($cached->getPostalCode());
        $this->assertNull($cached->getSubLocality());
        $this->assertInstanceOf('\Geocoder\Model\AdminLevelCollection', $cached->getAdminLevels());
        $adminLevels = $cached->getAdminLevels()->all();
        $this->assertTrue(is_array($adminLevels));
        $this->assertCount(2, $adminLevels);
        $this->assertInstanceOf('\Geocoder\Model\AdminLevel', $adminLevels[1]);
        $this->assertEquals('New York', $adminLevels[1]->getName());
        $this->assertEquals('NY', $adminLevels[1]->getCode());
        $this->assertInstanceOf('\Geocoder\Model\AdminLevel', $adminLevels[2]);
        $this->assertEquals('New York County', $adminLevels[2]->getName());
        $this->assertEquals('New York County', $adminLevels[2]->getCode());
        $this->assertEquals('France', $cached->getCountry()->getName());
        $this->assertEquals('FR', $cached->getCountry()->getCode());
        $this->assertNull($cached->getTimezone());
    }

    public function testFlush()
    {
        $mockRedis = $this->getMockBuilder('\Predis\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('flushDb'))
            ->getMock();

        $mockRedis
            ->expects($this->once())
            ->method('flushDb');

        $this->redis->setRedis($mockRedis);
        $this->redis->flush();
    }
}

class TestableRedis extends Redis
{
    public function setRedis($redis)
    {
        $this->redis = $redis;
    }

    public function setExpire($expire)
    {
        $this->expire = $expire;
    }
}
