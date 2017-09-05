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

use League\Geotools\Batch\BatchGeocoded;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class BatchGeocodedTest extends \League\Geotools\Tests\TestCase
{
	/**
	 * @var BatchGeocoded;
	 */
	protected $batchGeocoded;

	protected function setUp()
	{
		$this->batchGeocoded = new BatchGeocoded;
	}

	public function testFromArray()
	{
		$array = [
			'providerName' => 'Foo provider foo',
			'query'        => 'Bar query bar',
			'exception'    => 'Baz exception baz',
			'address'      => [],
		];

		$this->batchGeocoded->fromArray($array);

		$this->assertEquals('Foo provider foo', $this->batchGeocoded->getProviderName());
		$this->assertEquals('Bar query bar', $this->batchGeocoded->getQuery());
		$this->assertEquals('Baz exception baz', $this->batchGeocoded->getExceptionMessage());
		$this->assertInstanceOf('\Geocoder\Model\Address', $this->batchGeocoded->getAddress());
		$this->assertNull($this->batchGeocoded->getCoordinates());
		$this->assertNull($this->batchGeocoded->getLatitude());
		$this->assertNull($this->batchGeocoded->getLongitude());
	}

	public function testOtherMethodsAreCalledFromTheAddressObject()
	{
		$this->batchGeocoded->fromArray(['address' => []]);

		$this->assertNull($this->batchGeocoded->getProviderName());
		$this->assertNull($this->batchGeocoded->getQuery());
		$this->assertNull($this->batchGeocoded->getExceptionMessage());
		$this->assertNull($this->batchGeocoded->getCoordinates());
		$this->assertNull($this->batchGeocoded->getLatitude());
		$this->assertNull($this->batchGeocoded->getLongitude());
		$address = $this->batchGeocoded->getAddress();
		$this->assertInstanceOf('\Geocoder\Model\Address', $address);
		$this->assertNull($address->getCoordinates());
        $this->assertNull($address->getBounds());
        $this->assertInstanceOf('\Geocoder\Model\Country', $address->getCountry());
        $this->assertInstanceOf('\Geocoder\Model\AdminLevelCollection', $address->getAdminLevels());
	}
}