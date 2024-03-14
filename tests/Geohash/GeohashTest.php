<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Geohash;

use League\Geotools\Geohash\Geohash;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class GeohashTest extends \League\Geotools\Tests\TestCase
{
    protected $geohash;

    protected function setup(): void
    {
        $this->geohash = new Geohash;
    }

    /**
     * @dataProvider lengthsProvider
     */
    public function testEncodeShouldThrowException($length)
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The length should be between 1 and 12.');
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
     * @dataProvider invalidStringGeoHashesProvider
     */
    public function testDecodeShouldThrowStringException($geohash)
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The geo hash should be a string.');
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
     * @dataProvider invalidRangeGeoHashesProvider
     */
    public function testDecodeShouldThrowRangeException($geohash)
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The length of the geo hash should be between 1 and 12.');
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
     * @dataProvider invalidCharGeoHashesProvider
     */
    public function testDecodeShouldThrowRuntimeException($geohash)
    {
        $this->expectException(\League\Geotools\Exception\RuntimeException::class);
        $this->expectExceptionMessage('This geo hash is invalid.');
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
        $this->assertEqualsWithDelta($expectedBoundingBox[0][0], $boundingBox[0]->getLatitude(), 0.1, '');
        $this->assertEqualsWithDelta($expectedBoundingBox[0][1], $boundingBox[0]->getLongitude(), 0.1, '');

        $this->assertTrue(is_object($boundingBox[1]));
        $this->assertInstanceOf('\League\Geotools\Coordinate\Coordinate', $boundingBox[1]);
        $this->assertInstanceOf('\League\Geotools\Coordinate\CoordinateInterface', $boundingBox[1]);
        $this->assertEqualsWithDelta($expectedBoundingBox[1][0], $boundingBox[1]->getLatitude(), 0.1, '');
        $this->assertEqualsWithDelta($expectedBoundingBox[1][1], $boundingBox[1]->getLongitude(), 0.1, '');
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
        $this->assertEqualsWithDelta($expectedCoordinate[0], $coordinate->getLatitude(), 0.1, '');
        $this->assertEqualsWithDelta($expectedCoordinate[1], $coordinate->getLongitude(), 0.1, '');
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
        $this->assertEqualsWithDelta($expectedBoundingBox[0][0], $boundingBox[0]->getLatitude(), 0.1, '');
        $this->assertEqualsWithDelta($expectedBoundingBox[0][1], $boundingBox[0]->getLongitude(), 0.1, '');

        $this->assertTrue(is_object($boundingBox[1]));
        $this->assertInstanceOf('\League\Geotools\Coordinate\Coordinate', $boundingBox[1]);
        $this->assertInstanceOf('\League\Geotools\Coordinate\CoordinateInterface', $boundingBox[1]);
        $this->assertEqualsWithDelta($expectedBoundingBox[1][0], $boundingBox[1]->getLatitude(), 0.1, '');
        $this->assertEqualsWithDelta($expectedBoundingBox[1][1], $boundingBox[1]->getLongitude(), 0.1, '');
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

    /**
     * @dataProvider geohashesAndExpectedNeighborProvider
     */
    public function testGetNeighbors($geoHash, $expectedNeighbors)
    {
        $coordinate = $this->geohash->decode($geoHash);

        $this->assertEquals($expectedNeighbors, $coordinate->getNeighbors(true));
    }

    public function geohashesAndExpectedNeighborProvider()
    {
        return array(
            array(
                'b',
                array(
                    Geohash::DIRECTION_NORTH      => '0',
                    Geohash::DIRECTION_SOUTH      => '8',
                    Geohash::DIRECTION_WEST       => 'z',
                    Geohash::DIRECTION_EAST       => 'c',
                    Geohash::DIRECTION_NORTH_WEST => 'p',
                    Geohash::DIRECTION_NORTH_EAST => '1',
                    Geohash::DIRECTION_SOUTH_WEST => 'x',
                    Geohash::DIRECTION_SOUTH_EAST => '9',
                ),
            ),
            array(
                'u8vwy',
                array(
                    Geohash::DIRECTION_NORTH      => 'u8vxn',
                    Geohash::DIRECTION_SOUTH      => 'u8vww',
                    Geohash::DIRECTION_WEST       => 'u8vwv',
                    Geohash::DIRECTION_EAST       => 'u8vwz',
                    Geohash::DIRECTION_NORTH_WEST => 'u8vxj',
                    Geohash::DIRECTION_NORTH_EAST => 'u8vxp',
                    Geohash::DIRECTION_SOUTH_WEST => 'u8vwt',
                    Geohash::DIRECTION_SOUTH_EAST => 'u8vwx',
                ),
            ),
            array(
                'gcpvj0e5',
                array(
                    Geohash::DIRECTION_NORTH      => 'gcpvj0eh',
                    Geohash::DIRECTION_SOUTH      => 'gcpvj0e4',
                    Geohash::DIRECTION_WEST       => 'gcpvj0dg',
                    Geohash::DIRECTION_EAST       => 'gcpvj0e7',
                    Geohash::DIRECTION_NORTH_WEST => 'gcpvj0du',
                    Geohash::DIRECTION_NORTH_EAST => 'gcpvj0ek',
                    Geohash::DIRECTION_SOUTH_WEST => 'gcpvj0df',
                    Geohash::DIRECTION_SOUTH_EAST => 'gcpvj0e6',
                ),
            ),
            array(
                's0000000',
                array(
                    Geohash::DIRECTION_NORTH      => 's0000001',
                    Geohash::DIRECTION_SOUTH      => 'kpbpbpbp',
                    Geohash::DIRECTION_WEST       => 'ebpbpbpb',
                    Geohash::DIRECTION_EAST       => 's0000002',
                    Geohash::DIRECTION_NORTH_WEST => 'ebpbpbpc',
                    Geohash::DIRECTION_NORTH_EAST => 's0000003',
                    Geohash::DIRECTION_SOUTH_WEST => '7zzzzzzz',
                    Geohash::DIRECTION_SOUTH_EAST => 'kpbpbpbr',
                ),
            ),
        );
    }
}
