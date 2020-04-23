<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Batch;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Geocoder\Collection;
use Geocoder\Provider\AbstractProvider;
use Geocoder\Provider\Provider as ProviderInterface;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use League\Geotools\Batch\Batch;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class BatchTest extends \League\Geotools\Tests\TestCase
{
    protected $geocoder;
    protected $providers;
    protected $providersName;
    protected $data;
    protected $values;
    protected $coordinates;

    protected function setUp()
    {
        $this->data = array(
            'latitude'  => 48.8234055,
            'longitude' => 2.3072664,
        );

        $this->providers = array(
            new MockProvider('provider1'),
            new MockProvider('provider2'),
            new MockProvider('provider3'),
            new MockProvider('provider4'),
        );

        foreach ($this->providers as $provider) {
            $this->providersName[] = $provider->getName();
        }

        $this->values = array(
            'foo',
            'bar',
            'baz',
            'qux',
        );

        $this->coordinates = array(
            $this->getMockCoordinateReturns(array(1, 2)),
            $this->getMockCoordinateReturns(array(1, 2)),
            $this->getMockCoordinateReturns(array(1, 2)),
            $this->getMockCoordinateReturns(array(1, 2)),
        );

        $this->geocoder = $this->getMockGeocoderReturns($this->providers);
    }

    public function testConstructorShouldAcceptGeocoderInterface()
    {
        new TestableBatch($this->getStubGeocoder());
    }

    public function testConstructorShouldSetGeocoderInterface()
    {
        $batch = new TestableBatch($this->getStubGeocoder());
        $geocoder = $batch->getGeocoder();

        $this->assertTrue(is_object($geocoder));
        $this->assertInstanceOf('Geocoder\Geocoder', $geocoder);
    }

    public function testGeocodeShouldReturnBatchInterface()
    {
        $batch = new TestableBatch($this->geocoder);
        $batchReturned = $batch->geocode('foo');

        $this->assertTrue(is_object($batchReturned));
        $this->assertInstanceOf('League\Geotools\Batch\Batch', $batchReturned);
        $this->assertInstanceOf('League\Geotools\Batch\BatchInterface', $batchReturned);
        $this->assertSame($batch, $batchReturned);
    }

    public function testGeocodeShouldMadeCorrectTasksArrayToCompute()
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->geocode('foo');
        $tasks = $batch->getTasks();

        $this->assertTrue(is_array($tasks));
        $this->assertCount(count($this->providers), $tasks);
    }

    public function testGeocodeShouldMadeCorrectTasksArrayToComputeWithManyValues()
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->geocode($this->values);
        $tasks = $batch->getTasks();

        $this->assertTrue(is_array($tasks));
        $this->assertCount(count($this->providers) * count($this->values), $tasks);
    }

    /**
     * @expectedException \League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage The argument should be a string or an array of strings to geocode.
     * @dataProvider invalidValuesProvider
     */
    public function testGeocodeShouldThrowInvalidArgumentException($values)
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->geocode($values);
    }

    public function invalidValuesProvider()
    {
        return array(
            array(0),
            array(0.0),
            array(1234, 5678.0),
            array(array()),
            array(array(), array()),
            array(' '),
            array(' ', ' '),
        );
    }

    public function testReverseShouldReturnBatchInterface()
    {
        $batch = new TestableBatch($this->geocoder);
        $batchReturned = $batch->reverse($this->getStubCoordinate());

        $this->assertTrue(is_object($batchReturned));
        $this->assertInstanceOf('League\Geotools\Batch\Batch', $batchReturned);
        $this->assertInstanceOf('League\Geotools\Batch\BatchInterface', $batchReturned);
        $this->assertSame($batch, $batchReturned);
    }

    public function testReverseShouldMadeCorrectTasksArrayToCompute()
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->reverse($this->getStubCoordinate());
        $tasks = $batch->getTasks();

        $this->assertTrue(is_array($tasks));
        $this->assertCount(count($this->providers), $tasks);
    }

    public function testReverseShouldMadeCorrectTasksArrayToComputeWithManyCoordinates()
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->reverse($this->coordinates);
        $tasks = $batch->getTasks();

        $this->assertTrue(is_array($tasks));
        $this->assertCount(count($this->providers) * count($this->coordinates), $tasks);
    }

    /**
     * @expectedException \League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage The argument should be a Coordinate instance or an array of Coordinate instances to reverse.
     * @dataProvider coordinatesProvider
     */
    public function testReverseShouldThrowInvalidArgumentException($coordinates)
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->reverse($coordinates);
    }

    public function coordinatesProvider()
    {
        return array(
            array(0),
            array(0.0),
            array(1234, 5678.0),
            array(array()),
            array(array(), array()),
            array(' '),
            array(' ', ' '),
        );
    }

    public function testBatchGeocodeInSerie()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->geocode('foo')->serie();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInSerieWithCache()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->geocode('foo')
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->serie();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInSerieWithManyValues()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->geocode($this->values)->serie();

        $this->assertCount(count($this->providers) * count($this->values), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInSerieWithManyValuesWithCache()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->geocode($this->values)
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->serie();

        $this->assertCount(count($this->providers) * count($this->values), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInSerie()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->reverse($this->getStubCoordinate($this->data['latitude'], $this->data['longitude']))->serie();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInSerieWithCache()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->reverse($this->getStubCoordinate())
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->serie();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInSerieWithManyCoordinates()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->reverse($this->coordinates)->serie();

        $this->assertCount(count($this->providers) * count($this->coordinates), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInSerieWithManyCoordinatesWithCache()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->reverse($this->coordinates)
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->serie();

        $this->assertCount(count($this->providers) * count($this->coordinates), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInParallel()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch->geocode('foo')->parallel();

        $this->assertCount(count($this->providers), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInParallelWithCache()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch
            ->geocode('foo')
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->parallel();

        $this->assertCount(count($this->providers), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInParallelWithManyValues()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch->geocode($this->values)->parallel();

        $this->assertCount(count($this->providers) * count($this->values), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInParallelWithManyValuesWithCache()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch
            ->geocode($this->values)
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->parallel();

        $this->assertCount(count($this->providers) * count($this->values), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInParallel()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->reverse($this->getStubCoordinate($this->data['latitude'], $this->data['longitude']))->parallel();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInParallelWithCache()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->reverse($this->getStubCoordinate())
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->parallel();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInParallelWithManyCoordinates()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->reverse($this->coordinates)->parallel();

        $this->assertCount(count($this->providers) * count($this->coordinates), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInParallelWithManyCoordinatesWithCache()
    {
        $geocoder = $this->getMockGeocoderReturns($this->providers, $this->data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->reverse($this->coordinates)
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->parallel();

        $this->assertCount(count($this->providers) * count($this->coordinates), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals($this->data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($this->data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInSerieReturnNewGeocodedInstance()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->geocode('foo')->serie();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEmpty($providerResult->getLatitude());
            $this->assertEmpty($providerResult->getLongitude());
            $this->assertContains($providerResult->getProviderName(), $this->providersName);
            $this->assertEquals('foo', $providerResult->getQuery());
        }
    }

    public function testBatchGeocodeInSerieReturnNewGeocodedInstanceWithCache()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->geocode('foo')
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->serie();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals(48.8234055, $providerResult->getLatitude());
            $this->assertEquals(2.3072664, $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInParallelReturnNewGeocodedInstance()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch->geocode('foo')->parallel();

        $this->assertCount(count($this->providers), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEmpty($providerResult->getLatitude());
            $this->assertEmpty($providerResult->getLongitude());
            $this->assertContains($providerResult->getProviderName(), $this->providersName);
            $this->assertEquals('foo', $providerResult->getQuery());
        }
    }

    public function testBatchGeocodeInParallelReturnNewGeocodedInstanceWithCache()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch
            ->geocode('foo')
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->parallel();

        $this->assertCount(count($this->providers), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals(48.8234055, $providerResult->getLatitude());
            $this->assertEquals(2.3072664, $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInSerieReturnNewGeocodedInstanceWithManyValues()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->geocode($this->values)->serie();

        $this->assertCount(count($this->providers) * count($this->values), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEmpty($providerResult->getLatitude());
            $this->assertEmpty($providerResult->getLongitude());
            $this->assertContains($providerResult->getProviderName(), $this->providersName);
            $this->assertContains($providerResult->getQuery(), $this->values);
        }
    }

    public function testBatchGeocodeInSerieReturnNewGeocodedInstanceWithManyValuesWithCache()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->geocode($this->values)
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->serie();

        $this->assertCount(count($this->providers) * count($this->values), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals(48.8234055, $providerResult->getLatitude());
            $this->assertEquals(2.3072664, $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInParallelReturnNewGeocodedInstanceWithManyValues()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch->geocode($this->values)->parallel();

        $this->assertCount(count($this->providers) * count($this->values), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEmpty($providerResult->getLatitude());
            $this->assertEmpty($providerResult->getLongitude());
            $this->assertContains($providerResult->getProviderName(), $this->providersName);
            $this->assertContains($providerResult->getQuery(), $this->values);
        }
    }

    public function testBatchGeocodeInParallelReturnNewGeocodedInstanceWithManyValuesWithCache()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch
            ->geocode($this->values)
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->parallel();

        $this->assertCount(count($this->providers) * count($this->values), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals(48.8234055, $providerResult->getLatitude());
            $this->assertEquals(2.3072664, $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInSerieReturnNewGeocodedInstance()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->reverse($this->getMockCoordinateReturns(array(1, 2)))->serie();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEmpty($providerResult->getLatitude());
            $this->assertEmpty($providerResult->getLongitude());
            $this->assertContains($providerResult->getProviderName(), $this->providersName);
            $this->assertEquals($providerResult->getQuery(), '1, 2');
        }
    }

    public function testBatchReverseGeocodingInSerieReturnNewGeocodedInstanceWithCache()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->reverse($this->getMockCoordinateReturns(array(1, 2)))
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->serie();

        $this->assertCount(count($this->providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals(48.8234055, $providerResult->getLatitude());
            $this->assertEquals(2.3072664, $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInParallelReturnNewGeocodedInstance()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch->reverse($this->getMockCoordinateReturns(array(1, 2)))->parallel();

        $this->assertCount(count($this->providers), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEmpty($providerResult->getLatitude());
            $this->assertEmpty($providerResult->getLongitude());
            $this->assertContains($providerResult->getProviderName(), $this->providersName);
            $this->assertEquals($providerResult->getQuery(), '1, 2');
        }
    }

    public function testBatchReverseGeocodingInParallelReturnNewGeocodedInstanceWithCache()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch
            ->reverse($this->getMockCoordinateReturns(array(1, 2)))
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->parallel();

        $this->assertCount(count($this->providers), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals(48.8234055, $providerResult->getLatitude());
            $this->assertEquals(2.3072664, $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInSerieReturnNewGeocodedInstanceWithManyCoordinates()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->reverse($this->coordinates)->serie();

        $this->assertCount(count($this->providers) * count($this->coordinates), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEmpty($providerResult->getLatitude());
            $this->assertEmpty($providerResult->getLongitude());
            $this->assertContains($providerResult->getProviderName(), $this->providersName);
            $this->assertEquals($providerResult->getQuery(), '1, 2');
        }
    }

    public function testBatchReverseGeocodingInSerieReturnNewGeocodedInstanceWithManyCoordinatesWithCache()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch
            ->reverse($this->coordinates)
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->serie();

        $this->assertCount(count($this->providers) * count($this->coordinates), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals(48.8234055, $providerResult->getLatitude());
            $this->assertEquals(2.3072664, $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInParallelReturnNewGeocodedInstanceWithManyCoordinates()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch->reverse($this->coordinates)->parallel();

        $this->assertCount(count($this->providers) * count($this->coordinates), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEmpty($providerResult->getLatitude());
            $this->assertEmpty($providerResult->getLongitude());
            $this->assertContains($providerResult->getProviderName(), $this->providersName);
            $this->assertEquals($providerResult->getQuery(), '1, 2');
        }
    }

    public function testBatchReverseGeocodingInParallelReturnNewGeocodedInstanceWithManyCoordinatesWithCache()
    {
        $geocoder = $this->getMockGeocoderThrowException($this->providers);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch
            ->reverse($this->coordinates)
            ->setCache($this->getMockCacheReturns($this->getMockGeocodedReturns($this->data)))
            ->parallel();

        $this->assertCount(count($this->providers) * count($this->coordinates), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $providerResult);
            $this->assertEquals(48.8234055, $providerResult->getLatitude());
            $this->assertEquals(2.3072664, $providerResult->getLongitude());
        }
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage booooooooooo!
     */
    public function testSeriesShouldThrowException()
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->setTasks($tasks = array(
            function () {
                return \React\Promise\resolve('foo');
            },
            function () {
                throw new \RuntimeException('booooooooooo!');
            },
            function () {
                return \React\Promise\resolve('bar');
            },
        ))->geocode('foo')->serie();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage booooooooooo!
     */
    public function testParallelShouldThrowException()
    {
        $called = 0;

        $batch = new TestableBatch($this->geocoder);
        $batch->setTasks(array(
            function () {
                throw new \RuntimeException('booooooooooo!');
            },
            function () use (&$called) {
                $called++;
                return \React\Promise\resolve('foo');
            },
            function () {
                throw new \RuntimeException('booooooooooo!');
            },
            function () use (&$called) {
                $called++;
                return \React\Promise\resolve('bar');
            },
        ))->geocode('foo')->parallel();

        $this->assertSame(2, $called);
    }

    public function testIsCachedShouldReturnBatchGeocoded()
    {
        $batch  = new TestableBatch($this->geocoder);
        $cached = $batch->setCache($this->getMockCacheReturns($this->getStubBatchGeocoded()))->isCached('foo', 'bar');

        $this->assertTrue(is_object($cached));
        $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $cached);
    }

    public function testIsCachedShouldReturnFalse()
    {
        $batch  = new TestableBatch($this->geocoder);
        $cached = $batch->isCached('foo', 'bar');

        $this->assertFalse($cached);
    }

    public function testCacheShouldReturnBatchGeocoded()
    {
        $batch   = new TestableBatch($this->geocoder);
        $caching = $batch->setCache($this->getMockCacheReturns('foo'))->cache($this->getStubBatchGeocoded());

        $this->assertTrue(is_object($caching));
        $this->assertInstanceOf('League\Geotools\Batch\BatchGeocoded', $caching);
    }

    public function testSetCacheShouldReturnBatchInterface()
    {
        $batch          = new TestableBatch($this->geocoder);
        $batchWithCache = $batch->setCache(new ArrayCachePool());

        $this->assertTrue(is_object($batchWithCache));
        $this->assertInstanceOf('League\Geotools\Batch\Batch', $batchWithCache);
        $this->assertInstanceOf('League\Geotools\Batch\BatchInterface', $batchWithCache);
        $this->assertSame($batch, $batchWithCache);
        $this->assertTrue(is_object($batchWithCache->getCache()));
        $this->assertInstanceOf(CacheItemPoolInterface::class, $batchWithCache->getCache());
    }
}

class TestableBatch extends Batch
{
    public function getGeocoder()
    {
        return $this->geocoder;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function setTasks(array $tasks)
    {
        $this->tasks = $tasks;

        return $this;
    }

    public function getCache()
    {
        return $this->cache;
    }
}

class MockProvider extends AbstractProvider implements ProviderInterface
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        return new Collection([]);
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        return new Collection([]);
    }


}
