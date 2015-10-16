<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Cache;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Cache abstract class.
 *
 * Antoine Corcy <contact@sbin.dk>
 */
abstract class AbstractCache
{
    /**
     * Normalize an object to array.
     *
     * @param BatchGeocoded $object The BatchGeocoded object to normalize.
     *
     * @return array The normalized object.
     */
    protected function normalize($object)
    {
        $serializer = new Serializer([new ObjectNormalizer]);

        return $this->fixSerialization($serializer->normalize($object));
    }

    /**
     * Serialize an object to json.
     *
     * @todo There is an issue while serializing the Country object in JSON.
     * The country has the Country object (name and code) instead to have the country name.
     *
     * @param \League\Geotools\Batch\BatchGeocoded $object The BatchGeocoded object to serialize.
     *
     * @return string The serialized object in json.
     */
    protected function serialize($object)
    {
        $serializer = new Serializer([new ObjectNormalizer], [new JsonEncoder]);
        $serialized = $serializer->serialize($object, 'json');

        // transform to array to fix the serialization issue
        $serialized = json_decode($serialized, true);

        return json_encode($this->fixSerialization($serialized));
    }

    /**
     * Deserialize a json to BatchGeocoded object.
     *
     * @param string $json The json string to deserialize to BatchGeocoded object.
     *
     * @return array The deserialized json to array.
     */
    protected function deserialize($json)
    {
        return json_decode($json, true);
    }

    private function fixSerialization(array $serialized)
    {
        $serialized['address']['country'] = $serialized['address']['country']['name'];

        return $serialized;
    }
}
