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

use Geotools\Batch\BatchGeocoded;

/**
 * Memcached cache class.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Memcached extends AbstractCache implements CacheInterface
{
    /**
     * [$memcached description]
     * @var [type]
     */
    const DEFAULT_SERVER = 'localhost';

    /**
     * [$memcached description]
     * @var [type]
     */
    const DEFAULT_PORT = 11211;


    /**
     * [$memcached description]
     * @var [type]
     */
    protected $memcached;


    /**
     * Constructor.
     *
     * @param string $server The server address (optional).
     * @param string $port   The port number (optional).
     */
    public function __construct($server = self::DEFAULT_SERVER, $port = self::DEFAULT_PORT)
    {
        $this->memcached = new \Memcached();
        $this->memcached->addServer($server, $port);
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
            $this->serialize($geocoded)
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
