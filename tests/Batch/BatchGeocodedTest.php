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

use Geocoder\Model\Coordinates;
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

    public function testFromArrayWithRealData()
    {
        $array = array(
            'providerName'     => 'chain',
            'query'            => 'Julianaplein 1 , Noord-Holland, Amsterdam, Nederland',
            'exceptionMessage' => '',
            'address'          =>
                array(
                    'id'                => 'ChIJPd2JhH8JxkcRQ8jSHUchci0',
                    'locationType'      => 'ROOFTOP',
                    'resultType'        =>
                        array(
                            0 => 'premise',
                        ),
                    'formattedAddress'  => 'Amstelstation, Julianaplein 1, 1097 DN Amsterdam, Netherlands',
                    'airport'           => null,
                    'colloquialArea'    => null,
                    'intersection'      => null,
                    'naturalFeature'    => null,
                    'neighborhood'      => null,
                    'park'              => null,
                    'pointOfInterest'   => null,
                    'political'         => 'Netherlands',
                    'premise'           => 'Amstelstation',
                    'streetAddress'     => null,
                    'subpremise'        => null,
                    'ward'              => null,
                    'establishment'     => null,
                    'subLocalityLevels' =>
                        array(
                            1 =>
                                array(
                                    'level' => 1,
                                    'name'  => 'Amsterdam-Oost',
                                    'code'  => 'Amsterdam-Oost',
                                ),
                        ),
                    'providedBy'        => 'google_maps',
                    'coordinates'       =>
                        array(
                            'latitude'  => 52.346483799999987,
                            'longitude' => 4.9176187999999996,
                        ),
                    'bounds'            =>
                        array(
                            'south' => 52.345422000000013,
                            'west'  => 4.9166017000000002,
                            'north' => 52.347514699999998,
                            'east'  => 4.9187972000000002,
                        ),
                    'streetNumber'      => '1',
                    'streetName'        => 'Julianaplein',
                    'locality'          => 'Amsterdam',
                    'postalCode'        => '1097 DN',
                    'subLocality'       => 'Amsterdam-Oost',
                    'adminLevels'       =>
                        array(
                            1 =>
                                array(
                                    'level' => 1,
                                    'name'  => 'Noord-Holland',
                                    'code'  => 'NH',
                                ),
                            2 =>
                                array(
                                    'level' => 2,
                                    'name'  => 'Amsterdam',
                                    'code'  => 'Amsterdam',
                                ),
                        ),
                    'country'           => 'Netherlands',
                    'timezone'          => null,
                ),
            'coordinates'      =>
                array(
                    'latitude'  => 52.346483799999987,
                    'longitude' => 4.9176187999999996,
                ),
            'latitude'         => 52.346483799999987,
            'longitude'        => 4.9176187999999996,
        );

        $this->batchGeocoded->fromArray($array);

        $this->assertEquals('chain', $this->batchGeocoded->getProviderName());
        $this->assertEquals('Julianaplein 1 , Noord-Holland, Amsterdam, Nederland', $this->batchGeocoded->getQuery());
        $this->assertEquals('', $this->batchGeocoded->getExceptionMessage());
        $this->assertInstanceOf('\Geocoder\Model\Address', $this->batchGeocoded->getAddress());
        $this->assertEquals(new Coordinates(52.346483799999987, 4.9176187999999996), $this->batchGeocoded->getCoordinates());
        $this->assertEquals(52.346483799999987, $this->batchGeocoded->getLatitude());
        $this->assertEquals(4.9176187999999996, $this->batchGeocoded->getLongitude());
    }
}