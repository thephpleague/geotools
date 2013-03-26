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
use Predis\Client;

/**
 * Redis cache class.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Redis extends AbstractCache implements CacheInterface
{
    /**
     * The redis cache.
     *
     * @var Predis
     */
    protected $redis;


    /**
     * Constructor.
     *
     * @param array $client The client information (optional).
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $client = array())
    {
        try {
            $this->redis = new Client($client);
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getKey($providerName, $query)
    {
        return md5($providerName . $query);
    }

    /**
     * {@inheritDoc}
     */
    public function cache(BatchGeocoded $geocoded)
    {
        $this->redis->set(
            $this->getKey($geocoded->getProviderName(), $geocoded->getQuery()),
            $this->serialize($geocoded)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function isCached($providerName, $query)
    {
        $key = $this->getKey($providerName, $query);

        if (!$this->redis->exists($key)) {
            return false;
        }

        $cached = new BatchGeocoded();
        $cached->fromArray($this->deserialize($this->redis->get($key)));

        return $cached;
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->redis->flushDb();
    }
}
