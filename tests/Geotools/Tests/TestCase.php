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

use Geocoder\Result\Geocoded;

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
     * @return GeocoderInterface
     */
    protected function getMockGeocoderReturns(array $providers, array $data = array())
    {
        $geocoded = new Geocoded();

        if (!empty($data)) {
            $geocoded->fromArray($data);
        }

        $mock = $this->getMock('Geocoder\Geocoder');
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
            ->will($this->returnValue($geocoded));
        $mock
            ->expects($this->any())
            ->method('reverse')
            ->will($this->returnValue($geocoded));

        return $mock;
    }

    /**
     * @return GeocoderInterface
     */
    protected function getMockGeocoderThrowException(array $providers, array $data = array())
    {
        $mock = $this->getMock('Geocoder\Geocoder');
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
     * @return CoordinateInterface
     */
    protected function getMockCoordinateReturns(array $coordinate)
    {
        $mock = $this->getMock('\Geotools\Coordinate\CoordinateInterface');
        $mock
            ->expects($this->once())
            ->method('getLatitude')
            ->will($this->returnValue($coordinate[0]));
        $mock
            ->expects($this->once())
            ->method('getLongitude')
            ->will($this->returnValue($coordinate[1]));

        return $mock;
    }

    /**
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
