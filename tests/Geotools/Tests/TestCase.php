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

use Geotools\Batch\BatchGeocoded;
use Geotools\Coordinate\Ellipsoid;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return GeocoderInterface
     */
    protected function getStubGeocoder()
    {
        $stub = $this
            ->getMockBuilder('\Geocoder\GeocoderInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * @param array $providers
     * @param array $data
     * @return GeocoderInterface
     */
    protected function getMockGeocoderReturns(array $providers, array $data = array())
    {
        $batchGeocoded = new BatchGeocoded();

        if (!empty($data)) {
            $batchGeocoded->fromArray($data);
        }

        $mock = $this->getMock('\Geocoder\Geocoder');
        $mock
            ->expects($this->any())
            ->method('getProviders')
            ->will($this->returnValue($providers));
        $mock
            ->expects($this->any())
            ->method('using')
            ->will($this->returnSelf());
        $mock
            ->expects($this->any())
            ->method('geocode')
            ->will($this->returnValue($batchGeocoded));
        $mock
            ->expects($this->any())
            ->method('reverse')
            ->will($this->returnValue($batchGeocoded));

        return $mock;
    }

    /**
     * @param array $providers
     * @return GeocoderInterface
     */
    protected function getMockGeocoderThrowException(array $providers)
    {
        $mock = $this->getMock('\Geocoder\Geocoder');
        $mock
            ->expects($this->once())
            ->method('getProviders')
            ->will($this->returnValue($providers));
        $mock
            ->expects($this->atLeastOnce())
            ->method('using')
            ->will($this->returnSelf());
        $mock
            ->expects($this->any())
            ->method('geocode')
            ->will($this->throwException(new \Exception()));
        $mock
            ->expects($this->any())
            ->method('reverse')
            ->will($this->throwException(new \Exception()));

        return $mock;
    }

    /**
     * @return CoordinateInterface
     */
    protected function getStubCoordinate()
    {
        $stub = $this
            ->getMockBuilder('\Geotools\Coordinate\CoordinateInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * @param array $coordinate
     * @param Ellipsoid $ellipsoid
     * @return CoordinateInterface
     */
    protected function getMockCoordinateReturns(array $coordinate, Ellipsoid $ellipsoid = null)
    {
        $mock = $this->getMock('\Geotools\Coordinate\CoordinateInterface');
        $mock
            ->expects($this->any())
            ->method('getLatitude')
            ->will($this->returnValue($coordinate[0]));
        $mock
            ->expects($this->any())
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
     * @return ResultInterface
     */
    protected function getMockGeocoded($expects = null)
    {
        if (null === $expects) {
            $expects = $this->once();
        }

        $mock = $this->getMock('\Geocoder\Result\ResultInterface');
        $mock
            ->expects($expects)
            ->method('getCoordinates')
            ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * @param array $coordinate
     * @return ResultInterface
     */
    protected function getMockGeocodedReturns(array $coordinate)
    {
        $mock = $this->getMock('\Geocoder\Result\ResultInterface');
        $mock
            ->expects($this->once())
            ->method('getLatitude')
            ->will($this->returnValue($coordinate['latitude']));
        $mock
            ->expects($this->once())
            ->method('getLongitude')
            ->will($this->returnValue($coordinate['longitude']));

        return $mock;
    }
}
