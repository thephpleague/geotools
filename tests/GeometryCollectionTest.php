<?php

namespace League\Geotools\Tests;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateCollection;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\GeometryCollection;
use League\Geotools\Polygon\Polygon;
use PHPUnit\Framework\Attributes\Test;

class GeometryCollectionTest extends TestCase
{
    private $firstGeometry;

    private $secondGeometry;

    public function setup(): void
    {
        $this->firstGeometry = new Polygon(new CoordinateCollection([new Coordinate([1, 1])]));
        $this->secondGeometry = new Polygon(new CoordinateCollection([new Coordinate([2, 2])]));
    }

    #[Test]
    public function itShouldHaveThePrecisionOfTheLessPreciseGeometryComponent()
    {
        $this->firstGeometry->setPrecision(5);
        $this->secondGeometry->setPrecision(10);
        $array = [$this->firstGeometry, $this->secondGeometry];

        $collection = new SimpleGeometryCollection($array);

        $this->assertEquals(10, $collection->getPrecision());
    }

    #[Test]
    public function itShouldReturnNullIfItHasNoGeometry()
    {
        $collection = new SimpleGeometryCollection();

        $this->assertNull($collection->getCoordinate());
    }

    #[Test]
    public function itShouldReturnTheCoordinateOfItsFirstGeometry()
    {
        $array = [$this->firstGeometry, $this->secondGeometry];

        $collection = new SimpleGeometryCollection($array);

        $this->assertEquals(new Coordinate([1, 1]), $collection->getCoordinate());
    }

    #[Test]
    public function itShouldReturnAnArrayOfAllTheCoordinatesOfItsGeometries()
    {
        $array = [$this->firstGeometry, $this->secondGeometry];

        $collection = new SimpleGeometryCollection($array);

        $this->assertEquals(
            new CoordinateCollection([new Coordinate([1, 1]), new Coordinate([2, 2])]),
            $collection->getCoordinates()
        );
    }

    #[Test]
    public function itShouldReturnTheMergedBoundingBoxOfAllItsGeometries()
    {
        $array = [$this->firstGeometry, $this->secondGeometry];

        $collection = new SimpleGeometryCollection($array);

        $this->assertEquals(
            $this->firstGeometry->getBoundingBox()->merge($this->secondGeometry->getBoundingBox()),
            $collection->getBoundingBox()
        );
    }

    #[Test]
    public function itShouldReturnTheEllipsoidOfItsFirstGeometry()
    {
        $array = [$this->firstGeometry];

        $collection = new SimpleGeometryCollection($array);

        $this->assertEquals($this->firstGeometry->getEllipsoid(), $collection->getEllipsoid());
    }

    #[Test]
    public function itShouldBehaveAsAnArrayOfGeometries()
    {
        $array = [$this->firstGeometry];

        $collection = new SimpleGeometryCollection($array);

        $this->assertEquals($this->firstGeometry, $collection[0]);
        $this->assertFalse(isset($collection[99]));

        $collection['test'] = $this->secondGeometry;
        $this->assertEquals($this->secondGeometry, $collection['test']);

        unset($collection['test']);
        $this->assertFalse(isset($collection['test']));
    }

    #[Test]
    public function itShouldThrowAnExceptionWhenNotProvidedAGeometry()
    {
        $array = ['a'];

        $this->expectException('\InvalidArgumentException');

        $collection = new SimpleGeometryCollection($array);
    }

    #[Test]
    public function itShouldThrowAnExceptionWhenProvidedAnInvalidGeometry()
    {
        $array = [$this->firstGeometry];

        $collection = new SimpleGeometryCollection($array);

        $secondGeometry = new Polygon(
            new CoordinateCollection(
                [
                    new Coordinate(
                        [2, 2],
                        Ellipsoid::createFromName(Ellipsoid::AUSTRALIAN_NATIONAL)
                    )
                ]
            )
        );

        $this->expectException('\InvalidArgumentException');

        $collection->add($secondGeometry);
    }

    #[Test]
    public function itShouldBeCountable()
    {
        $array = [$this->secondGeometry];

        $collection = new SimpleGeometryCollection($array);

        $this->assertEquals(1, count($collection));
    }

    #[Test]
    public function itShouldOfferAccessToInnerElementsByKey()
    {
        $array = ['foo' => $this->firstGeometry];

        $collection = new SimpleGeometryCollection($array);
        $this->assertEquals($this->firstGeometry, $collection->get('foo'));

        $collection->set('dummy', $this->secondGeometry);
        $this->assertEquals($this->secondGeometry, $collection->get('dummy'));
        $this->assertNull($collection->get('bat'));

        $collection->remove('dummy');
        $this->assertNull($collection->get('dummy'));

        $collection->add($this->secondGeometry);
        $this->assertEquals($this->secondGeometry, $collection->get(0));

        $this->assertNull($collection->remove('dummy'));
    }

    #[Test]
    public function itShouldMergeCollections()
    {
        $array1 = ['foo' => $this->firstGeometry];
        $array2 = ['dummy' => $this->secondGeometry];

        $collection1 = new SimpleGeometryCollection($array1);
        $collection2 = new SimpleGeometryCollection($array2);

        $mergedCollection = $collection1->merge($collection2);

        $this->assertEquals($array1+$array2, $mergedCollection->toArray());

        $array1 = [$this->firstGeometry];
        $array2 = [$this->secondGeometry];

        $collection1 = new SimpleGeometryCollection($array1);
        $collection2 = new SimpleGeometryCollection($array2);

        $mergedCollection = $collection1->merge($collection2);

        $this->assertEquals(array_merge($array1, $array2), $mergedCollection->toArray());
    }

    #[Test]
    public function itShouldThorwAnExceptionWhenMergingDifferentTypesCollections()
    {
        $array1 = ['foo' => $this->firstGeometry];
        $array2 = ['dummy' => $this->secondGeometry];

        $collection1 = new SimpleGeometryCollection($array1);
        $collection2 = new OtherGeometryCollection($array2);

        $this->expectException('\InvalidArgumentException');

        $collection1->merge($collection2);
    }
}

class SimpleGeometryCollection extends GeometryCollection
{
    public function getGeometryType()
    {
        return 'SIMPLE';
    }
}

class OtherGeometryCollection extends GeometryCollection
{
    public function getGeometryType()
    {
        return 'OTHER';
    }
}