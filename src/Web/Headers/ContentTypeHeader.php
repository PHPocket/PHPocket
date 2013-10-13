<?php

namespace PHPocket\Web\Headers;

use asd,
    sadas;

/**
 * Object representing content-type header
 *
 * @package PHPocket\Web\Headers;
 */
class ContentTypeHeader extends AbstractHeader
{
    protected $_value;
    protected $_charset;

    const PREFIX = 'Content-Type';

    /**
     * Constructs header
     *
     * @param string      $value   desired content-type
     * @param string|null $charset desired charset for text
     */
    public function __construct($value, $charset = null)
    {
        parent::__construct(self::PREFIX);
        $this->_value = strtolower(trim($value));
        $this->_charset = $charset;
    }

    /**
     * Returns a value of header
     *
     * @return string
     */
    public function getValue()
    {
        if (!empty($this->_charset)) {
            return $this->_value . '; charset=' . $this->_charset;
        } else {
            return $this->_value;
        }
    }


}