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
 * Cache interface.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface CacheInterface
{
    /**
     * Return a unique key.
     *
     * @param string $providerName The name of the provider.
     * @param string $query        The query string.
     *
     * @return string The unique key.
     */
    public function getKey($providerName, $query);

    /**
     * Add into the cache.
     *
     * @param BatchGeocoded $geocoded The BatchGeocoded object to cache.
     *
     * @throws RuntimException
     */
    public function cache(BatchGeocoded $geocoded);

    /**
     * Check against the cache instance if any.
     *
     * @param string $providerName The name of the provider.
     * @param string $query        The query string.
     *
     * @return boolean|BatchGeocoded The BatchGeocoded if cached false otherwise.
     */
    public function isCached($providerName, $query);

    /**
     * Delete cached tuple.
     */
    public function flush();
}
