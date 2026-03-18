<?php

namespace League\Geotools\Tests;

use League\Geotools\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;

class ArrayCollectionTest extends TestCase
{
    #[Test]
    public function itShouldBeConvertibleToArray()
    {
        $array = ['foo', 'bar'];

        $collection = new ArrayCollection($array);

        $this->assertEquals($array, $collection->toArray());
    }

    #[Test]
    public function itShouldSerializeTheInnerElements()
    {
        $array = ['foo', 'bar'];

        $collection = new ArrayCollection($array);

        $this->assertEquals(json_encode($array), json_encode($collection));
    }

    #[Test]
    public function itShouldBehaveAsAnArray()
    {
        $array = ['foo', 'baz'=>'bar'];

        $collection = new ArrayCollection($array);

        $this->assertEquals('foo', $collection[0]);
        $this->assertFalse(isset($collection[99]));
        $this->assertTrue(isset($collection['baz']));
        $this->assertEquals('bar', $collection['baz']);

        $collection[99] = 'dummy';
        $this->assertEquals('dummy', $collection[99]);

        unset($collection[99]);
        $this->assertFalse(isset($collection[99]));
    }

    #[Test]
    public function itShouldBeCountable()
    {
        $array = ['foo', 'bar'];

        $collection = new ArrayCollection($array);

        $this->assertEquals(2, count($collection));
    }

    #[Test]
    public function itShouldOfferAccessToInnerElementsByKey()
    {
        $array = ['foo' => 'bar'];

        $collection = new ArrayCollection($array);
        $this->assertEquals('bar', $collection->get('foo'));

        $collection->set('dummy', 'baz');
        $this->assertEquals('baz', $collection->get('dummy'));
        $this->assertNull($collection->get('bat'));

        $collection->remove('dummy');
        $this->assertNull($collection->get('dummy'));

        $collection->add('baz');
        $this->assertEquals('baz', $collection->get(0));

        $this->assertNull($collection->remove('dummy'));
    }

    #[Test]
    public function itShouldMergeCollections()
    {
        $array1 = ['foo' => 'bar'];
        $array2 = ['dummy' => 'baz'];

        $collection1 = new ArrayCollection($array1);
        $collection2 = new ArrayCollection($array2);

        $mergedCollection = $collection1->merge($collection2);

        $this->assertEquals($array1+$array2, $mergedCollection->toArray());

        $array1 = ['bar'];
        $array2 = ['baz'];

        $collection1 = new ArrayCollection($array1);
        $collection2 = new ArrayCollection($array2);

        $mergedCollection = $collection1->merge($collection2);

        $this->assertEquals(array_merge($array1, $array2), $mergedCollection->toArray());
    }
}
