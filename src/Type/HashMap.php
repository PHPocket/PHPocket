<?php

namespace PHPocket\Type;


use PHPocket\Common\CollectionInterface;
use PHPocket\Common\EqualsInterface;

/**
 * Hash map implementation for PHP
 * Provides immutability except array access operations
 *
 * @package PHPocket\Type
 */
class HashMap implements
    \IteratorAggregate,
    CustomTypeInterface,
    CollectionInterface
{

    /**
     * Container for data
     *
     * @var array
     */
    protected $_data = null;


    /**
     * Constructor
     *
     * @param array|null $initialData
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($initialData = null)
    {
        if ($initialData !== null) {
            if ($initialData instanceof HashMap) {
                $this->_data = $initialData->_data;
            } elseif (is_array($initialData)) {
                $this->_data = $initialData;
            } elseif ($initialData instanceof \Traversable) {
                $this->_data = array();
                foreach ($initialData as $k => $v) {
                    $this->_data[$k] = $v;
                }
            } else {
                throw new \InvalidArgumentException(
                    'Initial data must be null|array|Traversable'
                );
            }
        } else {
            $this->_data = array();
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Returns true if current HashMap has
     * no elements
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }


    /**
     * Compares itself to $object and return true if
     * contents are equal
     *
     * @param mixed $object Object to compare
     * @return mixed
     */
    public function equals($object)
    {
        if ($object instanceof HashMap) {
            if ($this->count() !== $object->count()) {
                return false;
            }
            // Iterating over members
            foreach ($this as $k => $v) {
                if (!isset($object[$k])) {
                    // Key not exists
                    return false;
                }
                if ($v instanceof EqualsInterface) {
                    if (!$v->equals($object[$k])) {
                        // Values not equal
                        return false;
                    }
                } else {
                    if ($v != $object[$k]) {
                        // Values not equal
                        return false;
                    }
                }
            }
            // They are equal
            return true;
        }
        if (is_array($object)) {
            return $this->equals(new HashMap($object));
        }
        if ($object instanceof \Traversable) {
            return $this->equals(new HashMap($object));
        }

        return false;
    }

    /**
     * Returns true if HashMap has provided $offset
     *
     * @param string $offset
     * @return bool
     */
    public function containsKey($offset)
    {
        return $this->offsetExists($offset);
    }

    /**
     * Returns true if collection contains
     * provided value
     *
     * @param mixed $value
     * @return bool
     */
    public function containsValue($value)
    {
        if ($value instanceof EqualsInterface) {
            foreach ($this as $row) {
                if ($value->equals($row)) {
                    return true;
                }
            }
        } else {
            foreach ($this as $row) {
                if ($value == $row) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Computes the intersection of arrays with additional index check
     *
     * @param null|array|\Traversable $value
     * @return HashMap
     * @throws \InvalidArgumentException
     */
    public function intersect($value)
    {
        if (empty($value)) {
            return new self($this);
        }
        if (!is_array($value) && !($value instanceof \Traversable)) {
            throw new \InvalidArgumentException(
                'Expecting null|array|Traversable for diff'
            );
        }
        $value = new self($value);
        return new self(array_intersect_assoc($this->_data, $value->_data));
    }

    /**
     * Computes the difference of arrays with additional index check
     * Double-side diff!
     *
     * @param null|array|\Traversable $value
     * @return HashMap
     * @throws \InvalidArgumentException
     */
    public function diff($value)
    {
        if (empty($value)) {
            return new self($this);
        }
        if (!is_array($value) && !($value instanceof \Traversable)) {
            throw new \InvalidArgumentException(
                'Expecting null|array|Traversable for diff'
            );
        }
        $value = new self($value);
        return new self(
            array_merge(
                array_diff_assoc($this->_data, $value->_data),
                array_diff_assoc($value->_data, $this->_data)
            )
        );
    }

    /**
     * Merges current HashMap with provided Traversable and returns new HashMap
     * If key already exists, overwrites it
     *
     * @param null|array|\Traversable $value
     * @return HashMap
     * @throws \InvalidArgumentException
     */
    public function merge($value)
    {
        if (empty($value)) {
            return new self($this);
        }
        if (!is_array($value) && !($value instanceof \Traversable)) {
            throw new \InvalidArgumentException(
                'Expecting null|array|Traversable for merging'
            );
        }
        $clone = new self($this);
        foreach ($value as $k=>$v) {
            $clone->_data[$k] = $v;
        }
        return $clone;
    }

    /**
     * Applies callback to each element of array and returns
     * new HashMap
     *
     * @param callable $callback
     * @return HashMap
     */
    public function map($callback)
    {
        if ($this->isEmpty()) {
            return new self(array());
        }
        return new self(array_map($callback, $this->_data));
    }

    /**
     * Returns copy of HashMap with omitted empty values.
     * Check is based on PHP function empty(), thus following values are empty:
     * - null
     * - false
     * - 0
     * - '' (empty string)
     * - ...
     *
     * @return HashMap
     */
    public function trim()
    {
        if ($this->isEmpty()) {
            return new self(array());
        }
        $answer = array();
        foreach ($this->_data as $k => $v) {
            if (!empty($v)) {
                $answer[$k] = $v;
            }
        }

        return new self($answer);
    }

    /**
     * Returns array keys
     *
     * @return string[]
     */
    public function getKeys()
    {
        return array_keys($this->_data);
    }

    /**
     * Returns array values
     *
     * @return array
     */
    public function getValues()
    {
        return array_values($this->_data);
    }

    /**
     * Returns true only if all values of array
     * are not numeric, or HashMap is empty
     *
     * @return bool
     */
    public function isAssociative()
    {
        if ($this->count() === 0) {
            return true;
        }
        foreach ($this->getKeys() as $x) {
            if (is_numeric($x)) {
                return false;
            }
        }
        return true;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing
     * <b>Iterator</b> or <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     *
     * @throws \InvalidArgumentException if offset not found
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException(
                "Offset {$offset} not found"
            );
        }
        return $this->_data[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            throw new \InvalidArgumentException('Offset cannot be null');
        }
        $this->_data[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     *
     * @throws \InvalidArgumentException if offset not found
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException(
                "Offset {$offset} not found"
            );
        }

        unset ($this->_data[$offset]);
    }

}