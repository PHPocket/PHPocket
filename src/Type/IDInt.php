<?php

namespace PHPocket\Type;


class IDInt extends ID
{

    /**
     * Constructs an IDInt object
     *
     * @param int|ID $value
     *
     * @throws \InvalidArgumentException if $value is not integer
     */
    public function __construct($value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Empty ID not allowed');
        }
        // Wrapping
        $value = new ID($value);

        // Checking
        if (!$value->isSpecial()) {
            // Implicit cast will trigger an error
            // If there is not integer inside
            $value->getInt();
        }
        parent::__construct($value);
    }

    /**
     * Returns value of ID, always int
     *
     * @return int
     */
    public function getValue()
    {
        return parent::getValue();
    }

    /**
     * Returns value of ID
     *
     * @return int
     */
    public function getInt()
    {
        return $this->getValue();
    }



}