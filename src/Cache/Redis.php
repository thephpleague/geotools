<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Cache;

use League\Geotools\Exception\InvalidArgumentException;
use League\Geotools\Batch\BatchGeocoded;
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
     * The expire value for keys.
     *
     * @var integer
     */
    protected $expire;


    /**
     * Constructor.
     *
     * @param array   $client The client information (optional).
     * @param integer $expire The expire value in seconds (optional).
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $client = array(), $expire = 0)
    {
        try {
            $this->redis  = new Client($client);
            $this->expire = (int) $expire;
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
            $key = $this->getKey($geocoded->getProviderName(), $geocoded->getQuery()),
            $this->serialize($geocoded)
        );

        if ($this->expire > 0) {
            $this->redis->expire($key, $this->expire);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isCached($providerName, $query)
    {
        if (!$this->redis->exists($key = $this->getKey($providerName, $query))) {
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
