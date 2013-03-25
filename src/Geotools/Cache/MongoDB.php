<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Cache;

use Geotools\Exception\InvalidArgumentException;
use Geotools\Batch\BatchGeocoded;

/**
 * MongoDB cache class.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class MongoDB extends AbstractCache implements CacheInterface
{
    /**
     * The database name.
     *
     * @var string
     */
    const DATABASE = 'geotools';

    /**
     * The collection name.
     *
     * @var string
     */
    const COLLECTION = 'geotools_collection';


    /**
     * The collection to work with.
     *
     * @var MongoCollection
     */
    protected $collection;


    /**
     * Constructor.
     *
     * @param string $server     The server information (optional).
     * @param string $database   The database name (optional).
     * @param string $collection The collection name (optional).
     *
     * @throws InvalidArgumentException
     */
    public function __construct($server = null, $database = self::DATABASE, $collection = self::COLLECTION)
    {
        try {
            $mongoDB          = new \MongoClient($server);
            $database         = $mongoDB->$database;
            $this->collection = $database->$collection;
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Add into the cache.
     *
     * @param array $geocoded The normalized BatchGeocoded object.
     */
    private function add(array $geocoded)
    {
        $this->collection->insert($geocoded);
    }

    /**
     * Check if a BatchGeocoded object is already in the cache.
     *
     * @param BatchGeocoded $geocoded The BatchGeocoded to check.
     *
     * @return boolean Cached or not.
     */
    private function isCached(BatchGeocoded $geocoded)
    {
        $total = $this->collection->find(array(
            'providerName' => $geocoded->getProviderName(),
            'query'        => $geocoded->getQuery(),
        ))->count();

        return $total >= 1;
    }

    /**
     * {@inheritDoc}
     */
    public function check(BatchGeocoded $geocoded)
    {
        if ($this->isCached($geocoded)) {
            return $geocoded;
        }

        $this->add($this->normalize($geocoded));

        return $geocoded;
    }
}
