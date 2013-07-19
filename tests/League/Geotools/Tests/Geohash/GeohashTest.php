<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Geohash;

use League\Geotools\Tests\TestCase;
use League\Geotools\Geohash\Geohash;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class GeohashTest extends TestCase
{
    protected $geohash;

    protected function setUp()
    {
        $this->geohash = new Geohash();
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage The length should be between 1 and 12.
     * @dataProvider lengthsProvider
     */
    public function testEncodeShouldThrowException($length)
    {
        $this->geohash->encode($this->getStubCoordinate(), $length);
    }

    public function lengthsProvider()
    {
        return array(
            array(-1),
            array(0),
            array(''),
            array(' '),
            array('foo'),
            array(array()),
            array(13),
            array('13'),
        );
    }

    public function testEncodeShouldReturnTheSameGeohashInstance()
    {
        $encoded = $this->geohash->encode($this->getStubCoordinate());

        $this->assertTrue(is_object($encoded));
        $this->assertInstanceOf('\League\Geotools\Geohash\Geohash', $encoded);
        $this->assertInstanceOf('\League\Geotools\Geohash\GeohashInterface', $encoded);
        $this->assertSame($this->geohash, $encoded);
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage The geo hash should be a string.
     * @dataProvider invalidStringGeoHashesProvider
     */
    public function testDecodeShouldThrowStringException($geohash)
    {
        $this->geohash->decode($geohash);
    }

    public function invalidStringGeoHashesProvider()
    {
        return array(
            array(-1),
            array(0),
            array(1.0),
            array(array()),
        );
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage The length of the geo hash should be between 1 and 12.
     * @dataProvider invalidRangeGeoHashesProvider
     */
    public function testDecodeShouldThrowRangeException($geohash)
    {
        $this->geohash->decode($geohash);
    }

    public function invalidRangeGeoHashesProvider()
    {
        return array(
            array(''),
            array('bcdefghjkmnpqrstuvwxyz'),
            array('0123456789012'),
            array('bcd04324kmnpz'),
        );
    }

    /**
     * @expectedException League\Geotools\Exception\RuntimeException
     * @expectedExceptionMessage This geo hash is invalid.
     * @dataProvider invalidCharGeoHashesProvider
     */
    public function testDecodeShouldThrowRuntimeException($geohash)
    {
        $this->geohash->decode($geohash);
    }

    public function invalidCharGeoHashesProvider()
    {
        return array(
            array(' '),
            array('a'),
            array('i'),
            array('l'),
            array('o'),
            array('ø'),
            array('å'),
        );
    }

    public function testDecodeShouldReturnTheSameGeohashInstance()
    {
        $decoded = $this->geohash->decode('u09tu800gnqw');

        $this->assertTrue(is_object($decoded));
        $this->assertInstanceOf('\League\Geotools\Geohash\Geohash', $decoded);
        $this->assertInstanceOf('\League\Geotools\Geohash\GeohashInterface', $decoded);
        $this->assertSame($this->geohash, $decoded);
    }

    /**
     * @dataProvider coordinatesAndExpectedGeohashesAndBoundingBoxesProvider
     */
    public function testEncodedGetGeoHash($coordinate, $length, $expectedGeoHash)
    {
        $geohash = $this->geohash->encode($this->getMockCoordinateReturns($coordinate), $length)->getGeohash();

        $this->assertSame($length, strlen($geohash));
        $this->assertSame($expectedGeoHash, $geohash);
    }

    /**
     * @dataProvider coordinatesAndExpectedGeohashesAndBoundingBoxesProvider
     */
    public function testEncodedGetBoundingBox($coordinate, $length, $expectedGeoHash, $expectedBoundingBox)
    {
        $boundingBox = $this->geohash->encode($this->getMockCoordinateReturns($coordinate), $length)->getBoundingBox();

        $this->assertTrue(is_array($boundingBox));
        $this->assertTrue(is_object($boundingBox[0]));
        $this->assertInstanceOf('\League\Geotools\Coordinate\Coordinate', $boundingBox[0]);
        $this->assertInstanceOf('\League\Geotools\Coordinate\CoordinateInterface', $boundingBox[0]);
        $this->assertEquals($expectedBoundingBox[0][0], $boundingBox[0]->getLatitude(), '', 0.1);
        $this->assertEquals($expectedBoundingBox[0][1], $boundingBox[0]->getLongitude(), '', 0.1);

        $this->assertTrue(is_object($boundingBox[1]));
        $this->assertInstanceOf('\League\Geotools\Coordinate\Coordinate', $boundingBox[1]);
        $this->assertInstanceOf('\League\Geotools\Coordinate\CoordinateInterface', $boundingBox[1]);
        $this->assertEquals($expectedBoundingBox[1][0], $boundingBox[1]->getLatitude(), '', 0.1);
        $this->assertEquals($expectedBoundingBox[1][1], $boundingBox[1]->getLongitude(), '', 0.1);
    }

    public function coordinatesAndExpectedGeohashesAndBoundingBoxesProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                12,
                'u09tu800gnqw',
                array(
                    array(48.8232421875, 2.28515625),
                    array(48.8671875, 2.3291015625)
                )
            ),
            array(
                array(-28.8234055, 1.3072664),
                5,
                'k4buj',
                array(
                    array(-28.828125, 1.2744140625),
                    array(-28.7841796875, 1.318359375)
                )
            ),
            array(
                array(18.8234055, 22.3072664),
                7,
                's7rg5de',
                array(
                    array(18.822326660156, 22.306365966797),
                    array(18.823699951172, 22.307739257812)
                )
            ),
        );
    }

    /**
     * @dataProvider geohashesAndExpectedCoordinatesAndBoundingBoxesProvider
     */
    public function testDecodedGetCoordinate($geoHash, $expectedCoordinate)
    {
        $coordinate = $this->geohash->decode($geoHash)->getCoordinate();

        $this->assertTrue(is_object($coordinate));
        $this->assertInstanceOf('\League\Geotools\Coordinate\Coordinate', $coordinate);
        $this->assertInstanceOf('\League\Geotools\Coordinate\CoordinateInterface', $coordinate);
        $this->assertEquals($expectedCoordinate[0], $coordinate->getLatitude(), '', 0.1);
        $this->assertEquals($expectedCoordinate[1], $coordinate->getLongitude(), '', 0.1);
    }

    /**
     * @dataProvider geohashesAndExpectedCoordinatesAndBoundingBoxesProvider
     */
    public function testDecodedGetBoundingBox($geoHash, $expectedCoordinate, $expectedBoundingBox)
    {
        $boundingBox = $this->geohash->decode($geoHash)->getBoundingBox();

        $this->assertTrue(is_array($boundingBox));
        $this->assertTrue(is_object($boundingBox[0]));
        $this->assertInstanceOf('\League\Geotools\Coordinate\Coordinate', $boundingBox[0]);
        $this->assertInstanceOf('\League\Geotools\Coordinate\CoordinateInterface', $boundingBox[0]);
        $this->assertEquals($expectedBoundingBox[0][0], $boundingBox[0]->getLatitude(), '', 0.1);
        $this->assertEquals($expectedBoundingBox[0][1], $boundingBox[0]->getLongitude(), '', 0.1);

        $this->assertTrue(is_object($boundingBox[1]));
        $this->assertInstanceOf('\League\Geotools\Coordinate\Coordinate', $boundingBox[1]);
        $this->assertInstanceOf('\League\Geotools\Coordinate\CoordinateInterface', $boundingBox[1]);
        $this->assertEquals($expectedBoundingBox[1][0], $boundingBox[1]->getLatitude(), '', 0.1);
        $this->assertEquals($expectedBoundingBox[1][1], $boundingBox[1]->getLongitude(), '', 0.1);
    }

    public function geohashesAndExpectedCoordinatesAndBoundingBoxesProvider()
    {
        return array(
            array(
                'u09tu800gnqw',
                array(48.8234055, 2.3072664),
                array(
                    array(48.8232421875, 2.28515625),
                    array(48.8671875, 2.3291015625)
                )
            ),
            array(
                'k4buj',
                array(-28.8234055, 1.3072664),
                array(
                    array(-28.828125, 1.2744140625),
                    array(-28.7841796875, 1.318359375)
                )
            ),
            array(
                's7rg5dew',
                array(18.8234055, 22.3072664),
                array(
                    array(18.822326660156, 22.306365966797),
                    array(18.823699951172, 22.307739257812)
                )
            ),
        );
    }
}
