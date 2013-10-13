<?php

namespace PHPocket\Tests\Actions;


use PHPocket\Actions\HandlerInterface;
use PHPocket\Actions\RunnableInterface;
use PHPocket\Actions\SafeAction;

class SafeActionTest extends \PHPUnit_Framework_TestCase {

    public function testSafe()
    {
        $counter = new \stdClass();
        // No exception && finish set
        $x = new SafeAction(
            new InnerSafeActionProcessor($counter, false),
            new InnerSafeActionErrorHandler($counter),
            new InnerSafeActionFinish($counter)
        );
        $counter->i = 0;
        $x->run();
        $this->assertEquals(8 + 16, $counter->i);

        // Exception thrown
        $x = new SafeAction(
            new InnerSafeActionProcessor($counter, true),
            new InnerSafeActionErrorHandler($counter),
            new InnerSafeActionFinish($counter)
        );
        $counter->i = 0;
        $x->run();
        $this->assertEquals(128, $counter->i);

        // No exception but no finish
        $x = new SafeAction(
            new InnerSafeActionProcessor($counter, false),
            new InnerSafeActionErrorHandler($counter),
            null
        );
        $counter->i = 0;
        $x->run();
        $this->assertEquals(8, $counter->i);

        // Got exception but no handler
        $x = new SafeAction(
            new InnerSafeActionProcessor($counter, true),
            null,
            new InnerSafeActionFinish($counter)
        );
        $counter->i = 0;
        $x->run();
        $this->assertEquals(0, $counter->i);
    }

}

abstract class InnerByRefCounter
{
    protected $o;

    public function __construct( \stdClass $o )
    {
        $this->o = $o;
    }
}

class InnerSafeActionProcessor extends InnerByRefCounter implements RunnableInterface
{
    private $throw;

    public function __construct( \stdClass $o, $throw )
    {
        parent::__construct($o);
        $this->throw = $throw;
    }

    public function run()
    {
        if ($this->throw)
        {
            throw new \Exception();
        }
        $this->o->i += 8;
    }
}

class InnerSafeActionFinish extends InnerByRefCounter implements RunnableInterface
{
    public function run()
    {
        $this->o->i += 16;
    }
}


class InnerSafeActionErrorHandler extends InnerByRefCounter implements HandlerInterface
{
    /**
     * Receives data and handles it
     *
     * @param mixed $x Data to handle
     * @return mixed
     */
    public function handle($x)
    {
        if ($x != null && $x instanceof \Exception) {
            $this->o->i += 128;
        }
    }

}