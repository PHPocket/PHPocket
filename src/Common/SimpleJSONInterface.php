<?php
namespace PHPocket\Common;

/**
 * Object, implementing this interface, must contain toSimpleJSON method
 * which must return the simplest version of data, stored in object
 *
 * @overhead 0
 * @package PHPocket\Common
 */
interface SimpleJSONInterface
{
    /**
     * Returns the simplest version on internal object data in
     * JSON format
     *
     * @return string
     */
    public function toSimpleJSON();
}