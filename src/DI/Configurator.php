<?php

namespace PHPocket\DI;

use PHPocket\Type\HashMap;

/**
 * Tiny configurator, that can fill object properties
 * from values, received from associative array
 *
 * On setters phase, first looks for addProperty and only if
 * it not exits looks for setProperty
 *
 *
 * @package PHPocket\DI
 */
class Configurator
{

    /**
     * @var bool
     */
    protected $_settersFirst;
    /**
     * @var bool
     */
    protected $_throwExceptionsOnMiss;

    /**
     * Constructor
     *
     * @param bool $strict
     * @param bool $settersFirst
     */
    public function __construct($strict = true, $settersFirst = true)
    {
        $this->_settersFirst = (bool) $settersFirst;
        $this->_throwExceptionsOnMiss = (bool) $strict;
    }

    /**
     * Applies config on object
     * Depending on constructor flags it can first use setters and adders
     * before accessing to property directly
     *
     * @param mixed         $object
     * @param array|HashMap $config
     *
     * @throws \BadMethodCallException
     */
    public function apply($object, $config)
    {
        if ($this->_settersFirst) {
            $this->applySettersFirst($object, $config);
        } else {
            $this->applyPropertiesFirst($object, $config);
        }
    }

    /**
     * Applies config on object, but first looks at setters
     * and then on property
     * Priority:
     * addProperty > setProperty > property
     *
     * @param mixed         $object
     * @param array|HashMap $config
     * @throws \BadMethodCallException
     */
    public function applySettersFirst($object, $config)
    {
        $config = new HashMap($config);
        if ($config->isEmpty()) {
            // No configuration to apply
            return;
        }
        $config = $this->_applyOnSetters($object, $config);
        $config = $this->_applyOnProperties($object, $config);
        if ($this->_throwExceptionsOnMiss && !$config->isEmpty()) {
            throw new \BadMethodCallException(
                'Following properties presents in config'
                . ', but are missing in object: '
                . implode(',', $config->getKeys())
            );
        }
    }

    /**
     * Applies config on object, but looks at property first
     * Priority:
     * property > addProperty > setProperty
     *
     * @param mixed         $object
     * @param array|HashMap $config
     * @throws \BadMethodCallException
     */
    public function applyPropertiesFirst($object, $config)
    {
        $config = new HashMap($config);
        if ($config->isEmpty()) {
            // No configuration to apply
            return;
        }
        $config = $this->_applyOnProperties($object, $config);
        $config = $this->_applyOnSetters($object, $config);
        if ($this->_throwExceptionsOnMiss && !$config->isEmpty()) {
            throw new \BadMethodCallException(
                'Following properties presents in config'
                . ', but are missing in object: '
                . implode(',', $config->getKeys())
            );
        }
    }

    /**
     * Internal function, which tries to fill property using
     * adders and setters
     * Priority:
     * addProperty > setProperty
     *
     * @param mixed   $object
     * @param HashMap $config
     * @return HashMap not processed entries
     */
    protected function _applyOnSetters($object, HashMap $config)
    {
        $notProcessed = new HashMap();
        foreach($config as $attribute=>$argument) {
            $methodNameSet = 'set' . strtoupper($attribute[0]);
            $methodNameSet .= substr($attribute, 1);
            $methodNameAdd = 'add' . substr($methodNameSet, 3);
            if (method_exists($object, $methodNameAdd)) {
                // Adders first
                $object->$methodNameAdd($argument);
            } elseif (method_exists($object, $methodNameSet)) {
                // Setting
                $object->$methodNameSet($argument);
            } else {
                // Adding to not processed
                $notProcessed[$attribute] = $argument;
            }
        }

        return $notProcessed;
    }

    /**
     * Internal function, which tries to fill object properties
     * without setters
     *
     * @param mixed   $object
     * @param HashMap $config
     * @return HashMap not processed entries
     */
    protected function _applyOnProperties($object, HashMap $config)
    {
        $notProcessed = new HashMap();
        foreach($config as $attribute=>$argument) {
            if (property_exists($object, $attribute)) {
                // Setting
                $object->$attribute = $argument;
            } else {
                // Adding to not processed
                $notProcessed[$attribute] = $argument;
            }
        }

        return $notProcessed;
    }

}