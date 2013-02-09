<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests\Coordinate;

use Geotools\Tests\TestCase;
use Geotools\Coordinate\Coordinate;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class CoordinateTest extends TestCase
{
    /**
     * @expectedException Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage It should be an array or a class which implements Geocoder\Result\ResultInterface !
     * @dataProvider invalidCoordinatesProvider
     */
    public function testConstructorWithInvalidCoordinatesShouldThrowAnException($coordinates)
    {
        new Coordinate($coordinates);
    }

    public function invalidCoordinatesProvider()
    {
        return array(
            array(null),
            array('foo'),
            array('45.0'),
            array(123456),
            array(45.0),
            array(
                array()
            ),
            array(
                array('foo', 'bar', 'baz', 'qux')
            ),
        );
    }

    /**
     * @dataProvider validCoordinatesAndExpectedCoordinatesProvider
     */
    public function testConstructorWithValidCoordinatesShouldBeValid($coordinates, $expectedCoordinates)
    {
        $coordinate = new Coordinate($coordinates);

        $this->assertSame($expectedCoordinates[0], $coordinate->getLatitude());
        $this->assertSame($expectedCoordinates[1], $coordinate->getLongitude());
    }

    public function validCoordinatesAndExpectedCoordinatesProvider()
    {
        return array(
            array(
                array(1, 2),
                array(1.0, 2.0)
            ),
            array(
                array(-1, -2),
                array(-1.0, -2.0)
            ),
            array(
                array('1', '2'),
                array(1.0, 2.0),
            ),
            array(
                array('-1', '-2'),
                array(-1.0, -2.0)
            ),
            array(
                '10.0, 20.0',
                array(10.0, 20.0)
            ),
            array(
                '-10.0,-20.0',
                array(-10.0, -20.0)
            ),
        );
    }

    public function testConstructorWithResultInterfaceArgumentShouldBeValid()
    {
        new Coordinate($this->getMockGeocoded($this->never()));
    }

    /**
     * @dataProvider resultsProvider
     */
    public function testConstructorShouldReturnsLatitudeAndLongitude($result)
    {
        $geocoded = $this->getMockGeocodedReturns($result);
        $coordinate = new Coordinate($geocoded);

        $this->assertSame((double) $result['latitude'], $coordinate->getLatitude());
        $this->assertSame((double) $result['longitude'], $coordinate->getLongitude());
    }

    public function resultsProvider()
    {
        return array(
            array(
                array(
                    'latitude'  => 0.001,
                    'longitude' => 1,
                )
            ),
            array(
                array(
                    'latitude'  => -0.001,
                    'longitude' => -1,
                )
            ),
            array(
                array(
                    'latitude'  => '0.001',
                    'longitude' => '1',
                )
            ),
            array(
                array(
                    'latitude'  => '-0.001',
                    'longitude' => '-1',
                )
            ),
        );
    }

    /**
     * @dataProvider latitudesProvider
     */
    public function testSetLatitude($latitude)
    {
        $coordinate = new Coordinate($this->getMockGeocoded($this->never()));
        $coordinate->setLatitude($latitude);

        $this->assertSame((double) $latitude, $coordinate->getLatitude());
    }

    public function latitudesProvider()
    {
        return array(
            array(1),
            array(-1),
            array('1'),
            array('-1'),
            array(0.0001),
            array(-0.0001),
            array('0.0001'),
            array('-0.0001'),
        );
    }

    /**
     * @dataProvider longitudesProvider
     */
    public function testSetLongitude($longitude)
    {
        $coordinate = new Coordinate($this->getMockGeocoded($this->never()));
        $coordinate->setLongitude($longitude);

        $this->assertSame((double) $longitude, $coordinate->getLongitude());
    }

    public function longitudesProvider()
    {
        return array(
            array(1),
            array(-1),
            array('1'),
            array('-1'),
            array(0.0001),
            array(-0.0001),
            array('0.0001'),
            array('-0.0001'),
        );
    }
}
