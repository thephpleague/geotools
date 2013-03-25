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
    const COLLECTION = 'geotools_cache';


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
     * {@inheritDoc}
     */
    public function cache(BatchGeocoded $geocoded)
    {
        $this->collection->insert($this->normalize($geocoded));
    }

    /**
     * {@inheritDoc}
     */
    public function isCached($providerName, $value)
    {
        $result = $this->collection->findOne(array(
            'providerName' => $providerName,
            'query'        => $value,
        ));

        if (null === $result) {
            return false;
        }

        $cached = new BatchGeocoded();
        $cached->fromArray($result);

        return $cached;
    }
}
