<?php

namespace PHPocket\Actions;

/**
 * This interface is designed to provide a common protocol
 * for objects that wish to execute code while they are active
 *
 * @package Actions
 */
interface RunnableInterface
{

    /**
     * Performs an action
     *
     * @return void
     */
    public function run();
}