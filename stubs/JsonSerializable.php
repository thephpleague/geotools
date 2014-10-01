<?php

/**
 * The missing PHP 5.3 JsonSerializable Interface.
 *
 * @since 1.2
 */
interface JsonSerializable
{
    /**
     * @return mixed data which can be serialized by json_encode,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize();
}
