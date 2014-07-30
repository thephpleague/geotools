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

use League\Geotools\Batch\BatchGeocoded;

/**
 * Memcached cache class.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Memcached extends AbstractCache implements CacheInterface
{
    /**
     * The default server.
     *
     * @var string
     */
    const DEFAULT_SERVER = 'localhost';

    /**
     * The default port.
     *
     * @var integer
     */
    const DEFAULT_PORT = 11211;


    /**
     * The memcached instance.
     *
     * @var Memcached
     */
    protected $memcached;

    /**
     * The expire value for keys.
     *
     * @var integer
     */
    protected $expire;


    /**
     * Constructor.
     *
     * @param string  $server The server address (optional).
     * @param string  $port   The port number (optional).
     * @param integer $expire The expire value in seconds (optional).
     */
    public function __construct($server = self::DEFAULT_SERVER, $port = self::DEFAULT_PORT, $expire = 0)
    {
        $this->memcached = new \Memcached();
        $this->memcached->addServer($server, $port);
        $this->expire = (int) $expire;
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
        $this->memcached->set(
            $this->getKey($geocoded->getProviderName(), $geocoded->getQuery()),
            $this->serialize($geocoded),
            $this->expire
        );
    }

    /**
     * {@inheritDoc}
     */
    public function isCached($providerName, $query)
    {
        if (!$result = $this->memcached->get($this->getKey($providerName, $query))) {
            return false;
        }

        $cached = new BatchGeocoded();
        $cached->fromArray($this->deserialize($result));

        return $cached;
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->memcached->flush();
    }
}
