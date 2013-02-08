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

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return CoordinateInterface
     */
    protected function getStubCoordinate()
    {
        $stub = $this
            ->getMockBuilder('\Geotools\Coordinate\CoordinateInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $stub
            ->expects($this->any())
            ->method('getLatitude')
            ->will($this->returnSelf());

        return $stub;
    }

    /**
     * @return CoordinateInterface
     */
    protected function getMockCoordinateReturns(array $coordinate)
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
     * @param array $coordinate An array of latitude and longitude
     *
     * @return ResultInterface
     */
    protected function getMockGeocodedReturns(array $coordinate)
    {
        $mock = $this->getMock('\Geocoder\Result\ResultInterface');
        $mock
            ->expects($this->any())
            ->method('getLatitude')
            ->will($this->returnValue($coordinate['latitude']));
        $mock
            ->expects($this->any())
            ->method('getLongitude')
            ->will($this->returnValue($coordinate['longitude']));

        return $mock;
    }
}
