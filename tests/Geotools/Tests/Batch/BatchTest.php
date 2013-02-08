<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests\Batch;

use Geotools\Tests\TestCase;
use Geotools\Batch\Batch;
use Geocoder\Provider\ProviderInterface;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class BatchTest extends TestCase
{
    protected $geocoder;

    protected function setUp()
    {
        $this->geocoder = $this->getMockGeocoderReturns(array(
            new MockProvider('provider1'),
            new MockProvider('provider2'),
            new MockProvider('provider3'),
        ));
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
        $this->assertInstanceOf('Geocoder\GeocoderInterface', $geocoder);
    }

    public function testGeocodeShouldReturnBatchInterface()
    {
        $batch = new TestableBatch($this->geocoder);
        $batchReturned = $batch->geocode('foo');

        $this->assertTrue(is_object($batchReturned));
        $this->assertInstanceOf('Geotools\Batch\Batch', $batchReturned);
        $this->assertInstanceOf('Geotools\Batch\BatchInterface', $batchReturned);
        $this->assertSame($batch, $batchReturned);
    }

    public function testGeocodeShouldMadeCorrectTasksArrayToCompute()
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->geocode('foo');
        $tasks = $batch->getTasks();

        $this->assertCount(3, $tasks);
        $this->assertArrayHasKey('provider1', $tasks);
        $this->assertArrayHasKey('provider2', $tasks);
        $this->assertArrayHasKey('provider3', $tasks);
    }

    public function testReverseShouldReturnBatchInterface()
    {
        $batch = new TestableBatch($this->geocoder);
        $batchReturned = $batch->reverse($this->getStubCoordinate());

        $this->assertTrue(is_object($batchReturned));
        $this->assertInstanceOf('Geotools\Batch\Batch', $batchReturned);
        $this->assertInstanceOf('Geotools\Batch\BatchInterface', $batchReturned);
        $this->assertSame($batch, $batchReturned);
    }

    public function testReverseShouldMadeCorrectTasksArrayToCompute()
    {
        $batch = new TestableBatch($this->geocoder);
        $batch->reverse($this->getStubCoordinate());
        $tasks = $batch->getTasks();

        $this->assertCount(3, $tasks);
        $this->assertArrayHasKey('provider1', $tasks);
        $this->assertArrayHasKey('provider2', $tasks);
        $this->assertArrayHasKey('provider3', $tasks);
    }

    public function testBatchGeocodeInSerie()
    {
        $data = array(
            'latitude' => 48.8234055,
            'longitude' => '2.3072664',
        );

        $providers = array(
            new MockProvider('provider1'),
            new MockProvider('provider2'),
            new MockProvider('provider3'),
        );

        $geocoder = $this->getMockGeocoderReturns($providers, $data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->geocode('foo')->serie();

        $this->assertCount(count($providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('Geocoder\Result\Geocoded', $providerResult);
            $this->assertInstanceOf('Geocoder\Result\ResultInterface', $providerResult);
            $this->assertEquals($data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInSerie()
    {
        $data = array(
            'latitude' => 48.8234055,
            'longitude' => '2.3072664',
        );

        $providers = array(
            new MockProvider('provider1'),
            new MockProvider('provider2'),
            new MockProvider('provider3'),
        );

        $geocoder = $this->getMockGeocoderReturns($providers, $data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->reverse($this->getStubCoordinate())->serie();

        $this->assertCount(count($providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('Geocoder\Result\Geocoded', $providerResult);
            $this->assertInstanceOf('Geocoder\Result\ResultInterface', $providerResult);
            $this->assertEquals($data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchGeocodeInParallel()
    {
        $data = array(
            'latitude' => 48.8234055,
            'longitude' => '2.3072664',
        );

        $providers = array(
            new MockProvider('provider1'),
            new MockProvider('provider2'),
            new MockProvider('provider3'),
        );

        $geocoder = $this->getMockGeocoderReturns($providers, $data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInParallel = $batch->geocode('foo')->parallel();

        $this->assertCount(count($providers), $resultComputedInParallel);
        foreach ($resultComputedInParallel as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('Geocoder\Result\Geocoded', $providerResult);
            $this->assertInstanceOf('Geocoder\Result\ResultInterface', $providerResult);
            $this->assertEquals($data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($data['longitude'], $providerResult->getLongitude());
        }
    }

    public function testBatchReverseGeocodingInParallel()
    {
        $data = array(
            'latitude' => 48.8234055,
            'longitude' => '2.3072664',
        );

        $providers = array(
            new MockProvider('provider1'),
            new MockProvider('provider2'),
            new MockProvider('provider3'),
        );

        $geocoder = $this->getMockGeocoderReturns($providers, $data);
        $batch = new TestableBatch($geocoder);
        $resultComputedInSerie = $batch->reverse($this->getStubCoordinate())->parallel();

        $this->assertCount(count($providers), $resultComputedInSerie);
        foreach ($resultComputedInSerie as $providerResult) {
            $this->assertTrue(is_object($providerResult));
            $this->assertInstanceOf('Geocoder\Result\Geocoded', $providerResult);
            $this->assertInstanceOf('Geocoder\Result\ResultInterface', $providerResult);
            $this->assertEquals($data['latitude'], $providerResult->getLatitude());
            $this->assertEquals($data['longitude'], $providerResult->getLongitude());
        }
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
}

class MockProvider implements ProviderInterface
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getGeocodedData($address)
    {
        return array();
    }

    public function getReversedData(array $coordinates)
    {
        return array();
    }

    public function getName()
    {
        return $this->name;
    }
}
