<?php

namespace PHPocket\Data\ActiveRecord;

use PHPocket\Common\EqualsInterface;
use PHPocket\Common\IDInterface;
use PHPocket\Type\ID;

interface ActiveRecordInterface extends IDInterface, EqualsInterface
{
    /**
     * Sets the attribute of ActiveRecord
     *
     * @param string $key
     * @param mixed  $value
     * @return mixed
     *
     * @throws \Exception
     */
    public function setAttribute($key, $value);

    /**
     * Gets the attribute of ActiveRecord
     *
     * @param string $key
     * @return mixed
     *
     * @throws \Exception
     */
    public function getAttribute($key);

    /**
     * Save the changes (not all fields, just changes)
     *
     * @return void
     *
     * @throws \Exception
     */
    public function save();

    /**
     * Returns true, if data, represented by ActiveRecord is
     * mapped to existent database entry
     * For example, for non-existent or new entries this method
     * must return false
     *
     * @return boolean
     */
    public function exists();

    /**
     * Deletes current active record
     * Do not throw exception on NEW or EMPTY entries
     *
     * @return void
     *
     * @throws \Exception
     */
    public function delete();
}