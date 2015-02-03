<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The missing PHP 5.3 JsonSerializable Interface.
 */
interface JsonSerializable
{
    /**
     * Data which can be serialized by json_encode,
     * which is a value of any type other than a resource.
     *
     * @return mixed
     */
    function jsonSerialize();
}
