<?php

namespace PHPocket\Actions;

/**
 * Wrapper for PHP's callables
 *
 * @package Actions
 */
class CallableHandler implements HandlerInterface
{

    /**
     * @var callable
     */
    protected $_callable;

    /**
     * Creates wrapper for callable
     *
     * @param callable $callable
     * @throws \InvalidArgumentException
     */
    public function __construct($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Excpecting callable');
        }
        $this->_callable = $callable;
    }

    /**
     * Receives data and handles it
     *
     * @param mixed $x Data to handle
     * @return mixed
     */
    public function handle($x)
    {
        return call_user_func($this->_callable, $x);
    }


}