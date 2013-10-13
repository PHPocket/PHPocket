<?php
namespace PHPocket\Type;

/**
 * Special type to handle all IDs
 * Useful for Active Record and any other entities with identification
 * Allows to separate ID from other mixed keys/values
 *
 * This object is immutable
 *
 * @package PHPocket\Type
 */
class ID implements CustomTypeInterface
{
    /**
     * Private special constant
     * @var int
     */
    private static $_empty = 1;
    /**
     * Private special constant
     * @var int
     */
    private static $_new   = 2;

    /**
     * Is set, marks ID as special(NEW, EMPTY, etc)
     *
     * @var null
     */
    private $_special;

    /**
     * The value of ID
     *
     * @var mixed
     */
    private $_id;


    /**
     * Returns special NEW ID
     * This kind of IDs can be used to identify active records, representing
     * new data we must to insert into database
     *
     * @return ID
     */
    static public function getNew()
    {
        $id = new self( 1 );
        $id->_id = null;
        $id->_special = self::$_new;

        return $id;
    }

    /**
     * Returns special EMPTY ID
     * This kind of IDs can be used to identify entities with no data
     *
     * @return ID
     */
    static public function getEmpty()
    {
        $id = new self(1);

        $id->_id = null;
        $id->_special = self::$_empty;

        return $id;
    }

    /**
     * Constructs new ID object with provided value
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException if empty $value provided
     */
    public function __construct($value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Empty ID not allowed');
        }
        if (is_bool($value) || is_resource($value) || is_array($value)) {
            throw new \InvalidArgumentException('Argument not valid');
        }
        if ($value instanceof ID) {
            $this->_id = $value->_id;
            $this->_special = $value->_special;
        } else {
            if (is_string($value)
                && ctype_digit($value)
                && substr($value, 0, 1) !== '0'
            ) {
                // Special case for integers
                if (abs($value) < PHP_INT_MAX) {
                    $value = (int) $value;
                }
            }
            $this->_id = $value;
            $this->_special = null;
        }
    }

    /**
     * Returns true if current ID is special and EMPTY
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->_special === self::$_empty;
    }

    /**
     * Returns true if current ID is special and NEW
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->_special === self::$_new;
    }

    /**
     * Returns true if current ID is special and does not contain
     * any valid id information
     *
     * @return bool
     */
    public function isSpecial()
    {
        return $this->_special !== null;
    }

    /**
     * Returns value of ID
     *
     * @return mixed|string
     *
     * @throws \Exception if current ID is special
     */
    public function getValue()
    {
        if ($this->isSpecial()) {
            throw new \Exception('Cannot read value from special ID');
        }

        return $this->_id;
    }

    /**
     * Returns integer value of ID
     *
     * @return int
     * @throws \Exception if value of ID is not an integer, or
     * if value > PHP_INT_MAX or id current ID is special
     */
    public function getInt()
    {
        $value = (string) $this->getValue();
        if (!ctype_digit($value) || substr($value, 0, 1) === '0') {
            throw new \Exception('ID "' . $value . '" is not valid integer');
        }

        if (abs($value) > PHP_INT_MAX) {
            throw new \Exception(
                'ID "' . $value . '" > PHP_INT_MAX. Try to use 64bit platform'
            );
        }

        return (int) $value;
    }

    /**
     * Returns string value of ID
     *
     * @return string
     *
     * @throws \Exception if current ID is special
     */
    public function getString()
    {
        return (string) $this->_id;
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
        if (empty($object)) {
            if ($this->_special === self::$_empty) return true;
            return false;
        }
        $object = new ID($object);
        return
            $this->_special === $object->_special
            && $this->_id === $object->_id;
    }


    /**
     * Returns empty string if ID is special and string value otherwise
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isSpecial()) {
            return '';
        }
        return $this->getString();
    }
}