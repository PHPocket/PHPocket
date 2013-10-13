<?php

namespace PHPocket\Common;

use PHPocket\Type\ID;

/**
 * @package PHPocket\Common
 */
interface IDInterface
{
    /**
     * Returns ID of current object
     *
     * @return ID
     */
    public function getID();
}