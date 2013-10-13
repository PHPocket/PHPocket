<?php
namespace PHPocket\Web\Headers;
use PHPocket\Actions\RunnableInterface;
use PHPocket\Actions\SafeAction;

/**
 * Implementation of header Location
 * Can shutdown script if needed
 *
 * @package PHPocket\Web\Headers
 */
class Redirect extends AbstractHeader
{
    /**
     * @var string
     */
    protected $_url;
    /**
     * @var bool
     */
    protected $_finish;
    /**
     * @var RunnableInterface
     */
    protected $_callback;

    /**
     * Constructs a new header for redirect
     *
     * @param string $url             Url to redirect
     * @param bool   $finishAfterSend Shutdown script after sending
     */
    public function __construct($url, $finishAfterSend = true)
    {
        parent::__construct('Location');

        $this->_url = trim((string) $url);
        $this->_finish = (bool) $finishAfterSend;
    }

    /**
     * Returns an url to redirect to
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_url;
    }

    /**
     * Sends a header and finishes script if needed
     *
     * @return void
     */
    public function send()
    {
        parent::send();
        if ($this->_finish) {
            if (!empty($this->_callback)) {
                $this->_callback->run();
            }
            exit;
        }
    }

    /**
     * Overrides parents method and adds check for $this->_finish equality
     *
     * @param mixed $object
     * @return bool
     */
    public function equals($object)
    {
        if (!($object instanceof Redirect)) {
            return parent::equals($object);
        } else {
            return
                parent::equals($object)
                && $this->_finish === $object->_finish;
        }
    }


    /**
     * Turns autosutdown on or off
     *
     * @param bool $truefalse
     * @return void
     */
    public function setAutoshutdown($truefalse)
    {
        $this->_finish = (bool) $truefalse;
    }

    /**
     * Setups function, which must be called on script shutdown
     * caused by sending header
     *
     * @param callable|RunnableInterface $callbackOrRunnable
     * @return void
     */
    public function setBeforeShutdownCallback($callbackOrRunnable)
    {
        $this->_callback = new SafeAction($callbackOrRunnable);
    }
}