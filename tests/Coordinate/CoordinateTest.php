<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Coordinate;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class CoordinateTest extends \League\Geotools\Tests\TestCase
{
    /**
     * @dataProvider invalidCoordinatesProvider
     */
    public function testConstructorWithInvalidCoordinatesShouldThrowAnException($coordinates)
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('It should be a string, an array or a class which implements Geocoder\Model\Address !');
        new Coordinate($coordinates);
    }

    public function invalidCoordinatesProvider()
    {
        return array(
            array(null),
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
     * @dataProvider invalidStringCoordinatesProvider
     */
    public function testConstructorWithInvalidStringCoordinatesShouldThrowAnException($coordinates)
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('It should be a valid and acceptable ways to write geographic coordinates !');
        new Coordinate($coordinates);
    }

    public function invalidStringCoordinatesProvider()
    {
        return array(
            array(''),
            array(' '),
            array('_'),
            array('foo'),
            array('10.0'),
            array('10°, 20°'),
            array('10.0°, 20.0°'),
            array('47.0267747°, 002.3072664°'),
            array('47°01.60648\', 002°18.43598\''),
            array('47°01\'36.3888\", 002°18\'26.1590\"'),
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
                array('1.0', '2.0')
            ),
            array(
                array(-1, -2),
                array('-1.0', '-2.0')
            ),
            array(
                array('1', '2'),
                array('1.0', '2.0'),
            ),
            array(
                array('-1', '-2'),
                array('-1.0', '-2.0')
            ),
            array(
                '10.0, 20.0',
                array('10.0', '20.0')
            ),
            array(
                '-10.0,-20.0',
                array('-10.0', '-20.0')
            ),
            array(
                '40° 26.7717, -79° 56.93172',
                array('40.446195', '-79.948862')
            ),
            array(
                '40°26.7717 -79°56.93172',
                array('40.446195', '-79.948862')
            ),
            array(
                '40°26.7717S, 79°56.93172E',
                array('-40.446195', '79.948862')
            ),
            array(
                '40°26.7717N 79°56.93172W',
                array('40.446195', '-79.948862')
            ),
            array(
                'N40°26.7717W79°56.93172',
                array('40.446195', '-79.948862')
            ),
            array(
                'N 40° 26.7717 W 79° 56.93172',
                array('40.446195', '-79.948862')
            ),
            array(
                '40.446195N,79.948862W',
                array('40.446195', '-79.948862')
            ),
            array(
                '40.446195S 79.948862E',
                array('-40.446195', '79.948862')
            ),
            array(
                '40:26:46N, 079:56:55W',
                array('40.4461111111111', '-79.9486111111111')
            ),
            array(
                '40:26:46.302N, 079:56:55.903W',
                array('40.446195', '-79.9488619444444')
            ),
            array(
                '40:26:46.302s 079:56:55.903e',
                array('-40.446195', '79.9488619444444')
            ),
            array(
                '25°59.86′N,21°09.81′W',
                array('25.9976666666667', '-21.1635')
            ),
            array(
                '40°26′47″N 079°58′36″W',
                array('40.4463888888889', '-79.9766666666667')
            ),
            array(
                '40 26 47 n 079 58 36 w',
                array('40.4463888888889', '-79.9766666666667')
            ),
            array(
                '40d 26 47 n 079d 58 36 w',
                array('40.4463888888889', '-79.9766666666667')
            ),
            array(
                '40d 26′ 47″ N 079d 58′ 36″ W',
                array('40.4463888888889', '-79.9766666666667')
            ),
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorWithAddressArgumentShouldBeValid()
    {
        new Coordinate($this->createEmptyAddress());
    }

    /**
     * @dataProvider resultsProvider
     */
    public function testConstructorShouldReturnsLatitudeAndLongitude($result)
    {
        $geocoded = $this->createAddress($result);
        $coordinate = new Coordinate($geocoded);

        $this->assertSame((string) $result['latitude'], $coordinate->getLatitude());
        $this->assertSame((string) $result['longitude'], $coordinate->getLongitude());
    }

    public function resultsProvider()
    {
        return array(
            array(
                array(
                    'latitude'  => '0.001',
                    'longitude' => '1.0',
                )
            ),
            array(
                array(
                    'latitude'  => '-0.001',
                    'longitude' => '-1.0',
                )
            ),
            array(
                array(
                    'latitude'  => '0.001',
                    'longitude' => '1.0',
                )
            ),
            array(
                array(
                    'latitude'  => '-0.001',
                    'longitude' => '-1.0',
                )
            ),
        );
    }

    /**
     * @dataProvider latitudesWithExpectedLatitudesProvider
     */
    public function testNormalizeLatitude($latitude, $expectedLatitude)
    {
        $coordinate = new Coordinate($this->createEmptyAddress());

        $this->assertSame($expectedLatitude, $coordinate->normalizeLatitude($latitude));
    }

    public function latitudesWithExpectedLatitudesProvider()
    {
        return array(
            array('-180', '-90.0'),
            array('0', '0.0'),
            array('180', '90.0'),
        );
    }

    /**
     * @dataProvider longitudesWithExpectedLatitudesProvider
     */
    public function testNormalizeLongitude($longitude, $expectedLongitude)
    {
        $coordinate = new Coordinate($this->createEmptyAddress());

        $this->assertSame($expectedLongitude, $coordinate->normalizeLongitude($longitude));
    }

    public function longitudesWithExpectedLatitudesProvider()
    {
        return array(
            array(-500, '-140.0'),
            array(-360, '0.0'),
            array(-190, '170.0'),
            array(-180, '-180.0'),
            array('0', '0.0'),
            array(180, '180.0'),
            array(190, '-170.0'),
            array(360, '0.0'),
            array(500, '140.0'),
        );
    }

    /**
     * @dataProvider latitudesProvider
     */
    public function testSetLatitude($latitude)
    {
        $coordinate = new Coordinate($this->createEmptyAddress());
        $coordinate->setLatitude($latitude);
        $expected = (string) floatval($latitude);
        if (false === strpos($expected, '.')){
            $expected .= '.0';
        }

        $this->assertSame($expected, $coordinate->getLatitude());
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
        $coordinate = new Coordinate($this->createEmptyAddress());
        $coordinate->setLongitude($longitude);
        $expected = (string) floatval($longitude);
        if (false === strpos($expected, '.')){
            $expected .= '.0';
        }

        $this->assertSame($expected, $coordinate->getLongitude());
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

    public function testGetEllipsoid()
    {
        $WGS84      = Ellipsoid::createFromName(Ellipsoid::WGS84);
        $coordinate = new Coordinate($this->createEmptyAddress(), $WGS84);
        $ellipsoid  = $coordinate->getEllipsoid();

        $this->assertTrue(is_object($ellipsoid));
        $this->assertInstanceOf('League\Geotools\Coordinate\Ellipsoid', $ellipsoid);
    }

    public function testCreateFromStringWithoutAString()
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The given coordinates should be a string !');
        $coordinate = new Coordinate($this->createEmptyAddress());
        $coordinate->setFromString(123);
    }

    public function testCreateFromStringWithInvalidCoordinateString()
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('It should be a valid and acceptable ways to write geographic coordinates !');
        $coordinate = new Coordinate($this->createEmptyAddress());
        $coordinate->setFromString('foo');
    }

    public function testCreateFromStringWithValidCoordinatesShouldBeValid()
    {
        $coordinate = new Coordinate($this->createEmptyAddress());
        $coordinate->setFromString('40°26′47″N 079°58′36″W');

        $this->assertSame('40.4463888888889', $coordinate->getLatitude());
        $this->assertSame('-79.9766666666667', $coordinate->getLongitude());
    }
}
