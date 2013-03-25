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
 * Cache interface.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface CacheInterface
{
    /**
     * Add into the cache.
     *
     * @param BatchGeocoded $geocoded The BatchGeocoded object to cache.
     */
    public function cache(BatchGeocoded $geocoded);

    /**
     * Check against the cache instance if any.
     *
     * @param string $providerName The name of the provider.
     * @param string $value        The value of the query.
     *
     * @return boolean|BatchGeocoded The BatchGeocoded if cached false otherwise.
     */
    public function isCached($providerName, $value);
}
