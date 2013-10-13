<?php

namespace PHPocket\Actions;

/**
 * Safely executes a main action (RunnableInterface) and after then
 * runs onSuccess runnable (both provided in constructor) if there
 * were no exceptions is main action
 *
 * If there were exceptions in main action:
 * * if $onException handler set, invokes it and passes \Exception as argument
 * * if no handler set, suppresses an error, but terminates main action and
 *  do not run onSuccess action
 *
 * @package PHPocket\Actions
 */
class SafeAction implements RunnableInterface
{

    /**
     * @var RunnableInterface
     */
    protected $_action;
    /**
     * @var HandlerInterface
     */
    protected $_onException;
    /**
     * @var RunnableInterface
     */
    protected $_onNoError;

    /**
     * Creates safe action executor
     *
     * @param RunnableInterface      $action      Main action
     * @param HandlerInterface       $onException Handler to run on error
     * @param RunnableInterface|null $onSuccess   Action must run on finish
     */
    public function __construct(RunnableInterface $action,
        HandlerInterface $onException = null,
        RunnableInterface $onSuccess = null
    )
    {
        $this->_action = $action;
        $this->_onException = $onException;
        $this->_onNoError = $onSuccess;
    }

    /**
     * Performs an action
     *
     * @throws \Exception
     * @return void
     */
    public function run()
    {
        try {
            $this->_action->run();
            if (!empty($this->_onNoError)) {
                $this->_onNoError->run();
            }
        } catch ( \Exception $e ) {
            if (!empty($this->_onException)) {
                $this->_onException->handle($e);
            } else {
                // Ignoring
            }
        }
    }


}