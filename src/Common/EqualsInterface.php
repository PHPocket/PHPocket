<?php
namespace PHPocket\Common;

/**
 * Any object, providing equals(Object another) method
 * must implement this interface
 *
 * @overhead 0
 * @package PHPocket\Common
 */
interface EqualsInterface
{
    /**
     * Compares itself to $object and return true if
     * contents are equal
     *
     * @param mixed $object Object to compare
     * @return mixed
     */
    public function equals($object);
}