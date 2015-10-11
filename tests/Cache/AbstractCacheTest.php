<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Cache;

use League\Geotools\Batch\BatchGeocoded;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class AbstractCacheTest extends \League\Geotools\Tests\TestCase
{
    /**
     * @var TestableAbstractCache
     */
    protected $testableAbstractCache;

    /**
     * @var string
     */
    protected $json;

    protected function setUp()
    {
        $this->testableAbstractCache = new TestableAbstractCache;

        $this->json = <<<JSON
{"providerName":"google_maps","query":"Tagensvej 47, K\u00f8benhavn","exceptionMessage":null,"address":{"coordinates":{"latitude":55.699953,"longitude":12.552736},"latitude":55.699953,"longitude":12.552736,"bounds":{"south":55.699953,"west":12.552736,"north":55.699953,"east":12.552736,"defined":true},"streetNumber":"47","streetName":"Tagensvej","locality":"K\u00f8benhavn","postalCode":"2200","subLocality":"K\u00f8benhavn N","adminLevels":{"2":{"level":2,"name":"K\u00f8benhavn","code":"K\u00f8benhavn"}},"countryCode":"DK","timezone":null},"coordinates":{"latitude":55.699953,"longitude":12.552736},"latitude":55.699953,"longitude":12.552736}
JSON;
    }

    public function testSerialize()
    {
        $batchGeocoded = new BatchGeocoded;
        $batchGeocoded->fromArray([
            'providerName'     => 'google_maps',
            'query'            => 'Tagensvej 47, København',
            'exceptionMessage' => null,
            'address'          => [
                'latitude'  => 55.699953,
                'longitude' => 12.552736,
                'bounds'    => [
                    'south' => 55.699953,
                    'west'  => 12.552736,
                    'north' => 55.699953,
                    'east'  => 12.552736,
                ],
                'streetNumber' => '47',
                'streetName'   => 'Tagensvej',
                'locality'     => 'København',
                'postalCode'   => '2200',
                'subLocality'  => 'København N',
                'adminLevels'  => [
                    0 => [
                        'name'  => 'København',
                        'code'  => 'København',
                        'level' => 2,
                    ],
                ],
                'country'     => 'Denmark', // this is not serialized correctly and is ignored in the serializer
                'countryCode' => 'DK',
                'timezone'    => null,
            ],
        ]);

        $serialized = $this->testableAbstractCache->serialize($batchGeocoded);
        $this->assertSame($this->json, $serialized);

    }

    public function testDeserialize()
    {
        $array = [
            'providerName'     => 'google_maps',
            'query'            => 'Tagensvej 47, København',
            'exceptionMessage' => null,
            'address'          => [
                'latitude'  => 55.699953,
                'longitude' => 12.552736,
                'bounds'    => [
                    'south'   => 55.699953,
                    'west'    => 12.552736,
                    'north'   => 55.699953,
                    'east'    => 12.552736,
                    'defined' => true,
                ],
                'streetNumber' => '47',
                'streetName'   => 'Tagensvej',
                'locality'     => 'København',
                'postalCode'   => '2200',
                'subLocality'  => 'København N',
                'adminLevels'  => [
                    2 => [
                        'level' => 2,
                        'name'  => 'København',
                        'code'  => 'København',
                    ],
                ],
                // 'country'     => 'Denmark',
                'countryCode' => 'DK',
                'timezone'    => null,
                'coordinates' => [
                    'latitude'  => 55.699953,
                    'longitude' => 12.552736,
                ],
            ],
            'coordinates' => [
                'latitude'  => 55.699953,
                'longitude' => 12.552736,
            ],
            'latitude'  => 55.699953,
            'longitude' => 12.552736,
        ];

        $deserialized = $this->testableAbstractCache->deserialize($this->json);
        $this->assertEquals($array, $deserialized);
    }
}

class TestableAbstractCache extends \League\Geotools\Cache\AbstractCache
{
    public function serialize($object)
    {
        return parent::serialize($object);
    }

    public function deserialize($json)
    {
        return parent::deserialize($json);
    }
}
