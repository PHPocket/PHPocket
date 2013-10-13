<?php

namespace PHPocket\Web\Headers;
use PHPocket\Common\SimpleJSONInterface;
use PHPocket\Web\SendableInterface;

/**
 * Base class for all headers
 *
 * @package PHPocket\Web\Headers
 */
abstract class AbstractHeader implements SimpleJSONInterface, SendableInterface
{
    private static $_cliMode = null;

    /**
     * Type of header
     *
     * @var string
     */
    private $_type;

    /**
     * Function, that provides value of the header
     *
     * @return string
     */
    public abstract function getValue();

    /**
     * Constructs an header
     *
     * @param string $type Type of header
     */
    public function __construct($type)
    {
        $this->_type = trim((string)$type);
    }

    /**
     * Returns true if $object is header and internal fields equals to current
     * or $object is this equals to __toString of current header
     *
     * @param mixed $object
     * @return bool
     */
    public function equals($object)
    {
        if (empty($object)) {
            return false;
        }
        if (is_string($object)) {
            // Stringcast checking
            return $object === (string) $this;
        }
        if ($object instanceof AbstractHeader) {
            return
                $this->_type == $object->_type
                && $this->getValue() == $object->getValue();
        }
        return false;
    }

    /**
     * Returns type of current header
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Returns escaped getValue() for JSON
     *
     * @return string
     */
    protected function _escapeValueJSON()
    {
        if ($this->getValue() === null || $this->getValue() == '') {
            return 'null';
        }
        return '"' . str_replace(
            array('"'),
            array('\\"'),
            $this->getValue()
        ) . '"';
    }

    /**
     * Returns true if provided header has same type
     *
     * @param AbstractHeader $object
     * @return bool
     */
    public function sameType(AbstractHeader $object = null)
    {
        if ($object === null) {
            return false;
        }
        return $this->_type == $object->_type;
    }

    /**
     * Protected mode to check whenever can we send headers or not
     * Returns true of script not running in cli mode and headers
     * was not already sent
     *
     * @return bool
     */
    protected function _sendAllowed()
    {
        if (self::$_cliMode === true) {
            // No headers in cli mode
            return false;
        }
        if (self::$_cliMode === null) {
            // Detecting CLI mode
            self::$_cliMode = (php_sapi_name() == 'cli');
            return $this->_sendAllowed();
        }

        return !headers_sent();
    }

    /**
     * Sends a header if requirements met
     *
     * @return void
     */
    public function send()
    {
        if ($this->_sendAllowed()
            && !empty($this->_type)
            && $this->getValue() != null
        ) {
            header((string) $this);
        }
    }

    /**
     * Returns simple JSON entry for object
     *
     * @return string
     */
    public function toSimpleJSON()
    {
        return '{"' . $this->getType() . '":' . $this->_escapeValueJSON() . '}';
    }

    /**
     * Magic method
     *
     * @return null|string
     */
    public function __toString()
    {
        if ($this->getValue() === null) {
            return null;
        }
        return $this->_type . ': ' . $this->getValue();
    }

}