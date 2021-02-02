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

use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\ProviderAggregator;
use League\Geotools\Batch\BatchGeocoded;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Coordinate\Ellipsoid;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @return ProviderAggregator
     */
    protected function getStubGeocoder()
    {
        $stub = $this->createMock('\Geocoder\ProviderAggregator');

        return $stub;
    }

    /**
     * @param array $providers
     * @param array $data
     *
     * @return ProviderAggregator
     */
    protected function getMockGeocoderReturns(array $providers, array $data = array())
    {
        $addresses = new AddressCollection();

        if (!empty($data)) {
            $addresses = new AddressCollection([Address::createFromArray($data)]);
        }

        $mock = $this->createMock('\Geocoder\ProviderAggregator');
        $mock
            ->method('getProviders')
            ->will($this->returnValue($providers));
        $mock
            ->method('using')
            ->will($this->returnSelf());
        $mock
            ->method('geocode')
            ->will($this->returnValue($addresses));
        $mock
            ->method('reverse')
            ->will($this->returnValue($addresses));

        return $mock;
    }

    /**
     * @param array $providers
     *
     * @return ProviderAggregator
     */
    protected function getMockGeocoderThrowException(array $providers)
    {
        $mock = $this->createMock('\Geocoder\ProviderAggregator');
        $mock
            ->expects($this->once())
            ->method('getProviders')
            ->will($this->returnValue($providers));
        $mock
            ->method('using')
            ->will($this->returnSelf());
        $mock
            ->method('geocode')
            ->will($this->throwException(new \Exception));
        $mock
            ->method('reverse')
            ->will($this->throwException(new \Exception));

        return $mock;
    }

    /**
     * @param float|null $lat
     * @param float|null $lng
     * @return CoordinateInterface
     */
    protected function getStubCoordinate($lat = null, $lng = null)
    {
        $stub = $this->createMock('\League\Geotools\Coordinate\CoordinateInterface');

        if (null !== $lat) {
            $stub->method('getLatitude')->willReturn($lat);
        }
        if (null !== $lng) {
            $stub->method('getLongitude')->willReturn($lng);
        }

        return $stub;
    }

    /**
     * @param array $coordinate
     * @param Ellipsoid $ellipsoid
     *
     * @return CoordinateInterface
     */
    protected function getMockCoordinateReturns(array $coordinate, Ellipsoid $ellipsoid = null)
    {
        $mock = $this->createMock('\League\Geotools\Coordinate\CoordinateInterface');
        $mock
            ->method('getLatitude')
            ->will($this->returnValue($coordinate[0]));
        $mock
            ->method('getLongitude')
            ->will($this->returnValue($coordinate[1]));

        if ($ellipsoid) {
            $mock
                ->expects($this->atLeastOnce())
                ->method('getEllipsoid')
                ->will($this->returnValue($ellipsoid));
        }

        return $mock;
    }

    /**
     * @param $expects
     *
     * @return AddressCollection
     */
    protected function getMockGeocoded($expects = null)
    {
        if (null === $expects) {
            $expects = $this->once();
        }

        $mock = $this->createMock('\League\Geotools\Batch\BatchGeocoded');
        $mock
            ->expects($expects)
            ->method('getCoordinates')
            ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * @param array $coordinate
     *
     * @return AddressCollection
     */
    protected function getMockGeocodedReturns(array $coordinate)
    {
        $mock = $this->createMock('\League\Geotools\Batch\BatchGeocoded');
        $mock
            ->expects($this->atLeastOnce())
            ->method('getLatitude')
            ->will($this->returnValue($coordinate['latitude']));
        $mock
            ->expects($this->atLeastOnce())
            ->method('getLongitude')
            ->will($this->returnValue($coordinate['longitude']));

        return $mock;
    }

    /**
     * @return BatchGeocoded
     */
    protected function getStubBatchGeocoded()
    {
        $stub = $this->createMock('\League\Geotools\Batch\BatchGeocoded');

        $stub
            ->method('getProviderName')
            ->willReturn('provider');
        $stub
            ->method('getQuery')
            ->willReturn('query');

        return $stub;
    }

    /**
     * @param mixed $returnValue
     *
     * @return CacheItemPoolInterface
     */
    protected function getMockCacheReturns($returnValue)
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item
            ->method('get')
            ->willReturn($returnValue);
        $item
            ->method('isHit')
            ->willReturn(true);

        $mock = $this->createMock(CacheItemPoolInterface::class);
        $mock
            ->expects($this->atLeastOnce())
            ->method('getItem')
            ->willReturn($item);

        return $mock;
    }

	/**
     * Create an address object for testing
     *
     * @param array $data
     * @return Address|null
     */
    protected function createAddress(array $data)
    {
        return Address::createFromArray($data);
    }

	/**
     * Create an empty address object
     *
     * @return Address|null
     */
    protected function createEmptyAddress()
    {
        return $this->createAddress([]);
    }
}
