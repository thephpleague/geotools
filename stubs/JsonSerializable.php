<?php

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
