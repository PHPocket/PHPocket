<?php
namespace PHPocket\Actions;

/**
 * Classes, implementing interface, must handle received argument
 * and return an answer
 *
 * @package Actions
 */
interface HandlerInterface
{
    /**
     * Receives data and handles it
     *
     * @param mixed $x Data to handle
     * @return mixed
     */
    public function handle($x);
}