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
use League\Geotools\Cache\Redis;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ResdisTest extends TestCase
{
    protected $redis;

    protected function setUp()
    {
        if (!class_exists('Predis\\Client')) {
            $this->markTestSkipped('You need to install Predis.');
        }

        $this->redis = new TestableRedis();
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMEssage Invalid URI: foo.bar
     */
    public function testConstructorThrowsInvalidArgumentException()
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
        $mockRedis = $this->getMock('\Predis\Client', array('set', 'expire'));
        $mockRedis
            ->expects($this->once())
            ->method('set');
        $mockRedis
            ->expects($this->once())
            ->method('expire');

        $this->redis->setRedis($mockRedis);

        $mockGeocoded = $this->getMock('\League\Geotools\Batch\BatchGeocoded');
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
        $mockRedis = $this->getMock('\Predis\Client', array('exists'));
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
{"providerName":"google_maps","query":"Paris, France","exceptionMessage":"","coordinates":[48.856614,2.3522219],"latitude":48.856614,"longitude":2.3522219,"bounds":{"south":48.815573,"west":2.224199,"north":48.9021449,"east":2.4699208},"streetNumber":null,"streetName":null,"city":"Paris","zipcode":null,"cityDistrict":null,"county":"Paris","countyCode":"75","region":"\u00cele-De-France","regionCode":"IDF","country":"France","countryCode":"FR","timezone":null}
JSON
        ;

        $mockRedis = $this->getMock('\Predis\Client', array('exists', 'get'));
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
        $mockRedis = $this->getMock('\Predis\Client', array('flushDb'));
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
