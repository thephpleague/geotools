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
     * Check against the cache instance if any.
     *
     * @param BatchGeocoded $value The BatchGeocoded object to check against the cache instance.
     *
     * @return BatchGeocoded The BatchGeocoded object from the query or the cache instance.
     */
    public function check(BatchGeocoded $geocoded);
}
