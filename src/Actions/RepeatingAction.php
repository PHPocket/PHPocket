<?php

namespace PHPocket\Actions;

/**
 * Wrapper to actions, which must be executed several times
 *
 * @package Actions
 */
class RepeatingAction extends Action
{

    protected $_repeat;
    protected $_usleep;

    /**
     * Creates an action for callable or runnable
     *
     * @param callable|RunnableInterface $callableOrRunnable callback to run
     * @param int                        $repeatCount        amount of runs
     * @param float|double|int|null      $sleepInSeconds     sleep between runs
     *
     * @throws \InvalidArgumentException if any of arguments is not valid
     */
    public function __construct(
        $callableOrRunnable,
        $repeatCount,
        $sleepInSeconds = 0
    )
    {
        // Implicit call to parent constructor
        parent::__construct($callableOrRunnable);

        // Validating values
        if (!is_int($repeatCount) || $repeatCount < 1) {
            throw new \InvalidArgumentException(
                'Repeat amount must be a valid positive integer'
            );
        }
        if ($sleepInSeconds == null || $sleepInSeconds == false) {
            $sleepInSeconds = 0;
        }
        if ((!is_float($sleepInSeconds)
            && !is_double($sleepInSeconds)
            && !is_int($sleepInSeconds))
            || $sleepInSeconds < 0) {
            throw new \InvalidArgumentException(
                'Sleep time must be either float/double or int'
            );
        }
        // Setting repeat count
        $this->_repeat = $repeatCount;
        // Setting usleep value in microseconds
        if ( empty($sleepInSeconds) ) {
            $this->_usleep = 0;
        } else {
            $this->_usleep = (int) ( $sleepInSeconds * 1000000 );
        }
    }

    /**
     * Executes provided callback or runnable specified amount of runs
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < $this->_repeat; $i++) {
            parent::run();
            if ($this->_usleep > 0) {
                usleep($this->_usleep);
            }
        }
    }

}