<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

/**
 * Klasa zapytania SQL
 *
 * @author Piotr Zając
 */
class Select extends \Zend_Db_Select {

    /**
     * Ustawia część zapytania SQL
     * 
     * @param string $partName
     * @param mixed $partValue
     * @return \ZendY\Db\Select
     */
    public function setPart($partName, $partValue) {
        $this->_parts[$partName] = $partValue;
        return $this;
    }

    /**
     * Ustawia adapter bazodanowy
     * 
     * @param \ZendY\Db\Zend_Db_Adapter_Abstract $adapter
     * @return \ZendY\Db\Select
     */
    public function setAdapter(\Zend_Db_Adapter_Abstract $adapter) {
        $this->_adapter = $adapter;
        return $this;
    }

}

