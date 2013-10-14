<?php

namespace PHPocket\Data\ActiveRecord;

use \PDO as Provider;
use PHPocket\Common\EqualsInterface;
use PHPocket\Type\ID;
use Traversable;

/**
 * Lightweight ActiveRecord for PDO extension
 * No validators, just plain SQL works
 *
 * @package PHPocket\Data\ActiveRecord
 */
class PDO implements \IteratorAggregate, ActiveRecordInterface
{
    /**
     * @var Provider
     */
    protected $_db;

    /**
     * ID of object
     *
     * @var ID
     */
    protected $_id;

    /**
     * Contents of active record
     *
     * @var array|null
     */
    protected $_data = null;
    /**
     * Changes made to active record data
     * @var array
     */
    protected $_changes = array();

    protected $_tableName;

    protected $_idFieldName;

    /**
     * Creates new active record and passes mysqli object
     * as data source to it
     *
     * @param \PDO   $pdo
     * @param string $tableName
     * @param string $idFieldName
     *
     * @return PDO
     */
    static public function createNew(
        Provider $pdo,
        $tableName,
        $idFieldName = 'id')
    {
        /** @var PDO $x */
        $x = new static();
        $x->_id = ID::getNew();
        $x->_data = array();
        $x->_db = $pdo;
        $x->_tableName = $tableName;
        $x->_idFieldName = $idFieldName;

        return $x;
    }

    /**
     * Loads ActiveRecord from database
     * Must not throw exception if no entry found,
     * it must return ActiveRecord with exists() == false
     *
     * @param ID     $id
     * @param \PDO   $pdo
     * @param string $tableName
     * @param string $idFieldName
     *
     * @return PDO
     *
     * @throws \Exception
     */
    static public function load(
        ID $id,
        Provider $pdo,
        $tableName,
        $idFieldName = 'id' )
    {
        if ($id->isSpecial()) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        /** @var PDO $x */
        $x = new static();
        $x->_id = ID::getEmpty();
        $x->_db = $pdo;
        $x->_tableName = $tableName;
        $x->_idFieldName = $idFieldName;
        $x->_data = null;
        // Reading data from MySQLi
        $stmtSQL = 'SELECT * FROM `' . $x->_tableName . '` WHERE'
            . ' `' . $x->_idFieldName . '` = ? '
            . ' LIMIT 1';
        $stmt = $x->_db->prepare($stmtSQL);
        if ($stmt === false) {
            throw new \Exception('Bad schema or id key name');
        }
        try {
            $stmt->bindValue(1, $id->getValue());
            if ($stmt->execute()) {
                $data = $stmt->fetch(Provider::FETCH_ASSOC);
                if (!empty($data)) {
                    $x->_id = $id;
                    $x->_data = $data;
                }
                $stmt->closeCursor();
            }
        } catch (\Exception $e){
            // Ignoring exception
        }

        return $x;
    }

    /**
     * Protected constructor
     */
    protected function __construct()
    {
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {

        return new \ArrayIterator(clone $this->_data);
    }


    /**
     * Returns true, if data, represented by ActiveRecord is
     * mapped to existent database entry
     * For example, for non-existent or new entries this method
     * must return false
     *
     * @return boolean
     *
     * @throws \BadMethodCallException
     */
    public function exists()
    {
        if ($this->getID()->isEmpty()) {
            throw new \BadMethodCallException('Active record is empty');
        }
        return ! $this->getID()->isSpecial();
    }

    /**
     * Returns number of headers inside
     *
     * @return int
     */
    public function count()
    {
        if ($this->_data === null) return 0;
        return count($this->_data);
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
     *
     * @throws \BadMethodCallException
     */
    public function offsetExists($offset)
    {
        if ($this->getID()->isEmpty()) {
            throw new \BadMethodCallException('Active record is empty');
        }
        return array_key_exists($offset, $this->_data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            return;
        }
        unset($this->_data[$offset]);
        if (isset($this->_changes[$offset])) {
            unset($this->_changes[$offset]);
        }
    }

    /**
     * Returns true if collection contains
     * provided value
     *
     * @param mixed $value
     * @return bool
     *
     * @throws \BadMethodCallException
     */
    public function containsValue($value)
    {
        if ($this->getID()->isEmpty()) {
            throw new \BadMethodCallException('Active record is empty');
        }
        foreach ($this->_data as $k => $v){
            if ($v instanceof EqualsInterface) {
                if ($v->equals($value)) {
                    return true;
                }
            } elseif ($value instanceof EqualsInterface) {
                if ($value->equals($v)) {
                    return true;
                }
            } elseif ($v == $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if collection does not contain
     * any elements
     *
     * @return bool
     */
    public function isEmpty()
    {
        if ($this->getID()->isEmpty()) return true;
        return $this->_data == null || $this->count() == 0;
    }


    /**
     * Returns ID if current object
     *
     * @return ID
     */
    public function getID()
    {
        return $this->_id;
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
        if ($object === null || !($object instanceof PDO)) {
            return false;
        }
        /** @var PDO $object */
        return
            $object->_tableName === $this->_tableName
            && $object->getID()->equals($this->getID());
    }

    /**
     * Gets the attribute of ActiveRecord
     *
     * @param string $key
     * @return mixed
     *
     * @throws \Exception
     */
    public function offsetGet($key)
    {
        if (!$this->offsetExists($key)) {
            throw new \InvalidArgumentException(
                $key . ' not found in active record'
            );
        }

        return $this->_data[$key];
    }

    /**
     * Sets the attribute of ActiveRecord
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     *
     * @throws \Exception
     */
    public function offsetSet($key, $value)
    {
        if ($this->getID()->isEmpty()) {
            throw new \BadMethodCallException('Active record is empty');
        }
        if (!array_key_exists($key, $this->_data) && !$this->getID()->isNew()) {
            // Data not set and active record not new
            throw new \InvalidArgumentException(
                $key . ' not found in active record'
            );
        }

        $this->_data[$key] = $value;
        $this->_changes[$key] = $value;
    }

    /**
     * Save the changes (not all fields, just changes)
     *
     * @return void
     *
     * @throws \Exception
     */
    public function save()
    {
        if (count($this->_changes) === 0) {
            // No changes
            return;
        }
        $keys = array_keys($this->_changes);
        $values = array_values($this->_changes);
        // Generating statement
        if ($this->getID()->isNew()) {
            $stmtSQL = 'INSERT INTO `' . $this->_tableName . '` ';
            $stmtSQL .= '(`' . implode('`,`', $keys) . '`)';
            $stmtSQL .= ' VALUES (';
            $stmtSQL .= str_repeat('?,', count($this->_changes) - 1);
            $stmtSQL .= '?)';
        } else {
            $stmtSQL = 'UPDATE `' . $this->_tableName . '`';
            $stmtSQL .= ' SET ';
            for ($i=0; $i<count($keys); $i++) {
                if ($i>0) {
                    $stmtSQL .= ',';
                }
                $stmtSQL .= '`' . $keys[$i] . '`=?';
            }
        }

        $stmt = $this->_db->prepare($stmtSQL);
        if ($stmt===false) {
            throw new \Exception('Bad schema or id field name');
        }
        // Binding params
        foreach ($values as $k=>$v) {
            if ($v === null) {
                $stmt->bindValue($k+1, null, Provider::PARAM_NULL);
            } elseif (is_int($v)) {
                $stmt->bindParam($k+1, $v, Provider::PARAM_INT);
            } else {
                $stmt->bindValue($k+1, (string) $v, Provider::PARAM_STR);
            }
        }
        // Executing
        if (!$stmt->execute()) {
            $info = $stmt->errorInfo();
            $code = (
                isset($info, $info[1]) && !empty($info[1]) ? $info[1] : 0
            );
            $message = (
                isset($info, $info[2]) && !empty($info[2]) ? $info[2] : 0
            );
            throw new \Exception($message, $code);
        }

        // If inserted
        if ($this->getID()->isNew()) {
            if (isset($this->_changes[$this->_idFieldName])) {
                $this->_id = new ID(
                    $this->_changes[$this->_idFieldName]
                );
            } else {
                $this->_id = new ID($this->_db->lastInsertId());
            }
        } else {
            // Id can be changed
            $this->_id = new ID($this->_data[$this->_idFieldName]);
        }

        // Erasing changes
        $this->_changes = array();
    }

    /**
     * Deletes current active record
     * Do not throw exception on NEW or EMPTY entries
     *
     * @return void
     *
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->getID()->isSpecial()) {
            return;
        }

        $stmtSQL = 'DELETE FROM `' . $this->_tableName .  '`';
        $stmtSQL .= ' WHERE `' . $this->_idFieldName .'` = ?';

        $stmt = $this->_db->prepare($stmtSQL);
        if ($stmt===false) {
            throw new \Exception('Bad schema or id field name');
        }
        $stmt->bindValue(1, $this->getID()->getValue());
        $stmt->execute();

        $this->_id = ID::getEmpty();
    }


}