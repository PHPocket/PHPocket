<?php
namespace PHPocket\Util;
use PHPocket\Common\EqualsInterface;

/**
 * Utility class for checking that provided
 * arguments are equal
 *
 * @package PHPocket\Util
 */
class EqualityValidator implements EqualsInterface
{

    /**
     * @var bool
     */
    private $_strictPrimitives;

    /**
     * Constructor
     * If set to true, strict (===) equality check used for
     * primitive types
     *
     * @param bool $strict
     */
    public function __construct($strict = true)
    {
        $this->_strictPrimitives = (bool) $strict;
    }

    /**
     * Returns true if validators are equal
     *
     * @param mixed $o
     * @return bool
     */
    public function equals($o)
    {
        return $o !== null
            && is_object($o)
            && $o instanceof EqualityValidator
            && $o->_strictPrimitives === $this->_strictPrimitives;
    }

    /**
     * Returns true only if all provided to function
     * elements equals to each other
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return bool
     */
    public function areEqual($a, $b)
    {
        if ($a === null && $b === null) {
            // Nulls are equal
            return true;
        }
        if ($a === null || $b === null) {
            // One of arguments is null
            return false;
        }

        if (is_object($a) && $a instanceof EqualsInterface) {
            return $a->equals($b);
        }
        if (is_object($b) && $b instanceof EqualsInterface) {
            return $b->equals($a);
        }

        if (is_array($a) && !is_array($b)) {
            return false;
        }
        if (is_array($b) && !is_array($a)) {
            return false;
        }

        if ($this->_strictPrimitives) {
            return $a === $b;
        } else {
            return $a == $b;
        }
    }

    /**
     * Returns true if needle found inside array
     *
     * @param array|\Traversable $array
     * @param mixed              $needle
     * @param bool               $lookInKeys <p>if true, looks for needle
     * inside keys instead of values</p>
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function inArray($array, $needle, $lookInKeys = false)
    {
        if (empty($array)) {
            return false;
        }
        if (!is_array($array) && !$array instanceof \Traversable) {
            throw new \InvalidArgumentException(
                'Expecting array or Traversable'
            );
        }

        foreach ($array as $key => $value ) {
            if ($lookInKeys && $this->areEqual($needle, $key)) {
                return true;
            }
            if (!$lookInKeys && $this->areEqual($needle, $value)) {
                return true;
            }
        }

        return false;
    }

}