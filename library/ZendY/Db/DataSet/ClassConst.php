<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Msg;

/**
 * Zbiór stałych klasy
 *
 * @author Piotr Zając
 */
class ClassConst extends ArraySet {
    /**
     * Kolumny zbioru
     */

    const COL_NAME = 'name';
    const COL_VALUE = 'value';

    /**
     * Obiekt do wyłuskania stałych z klasy
     * 
     * @var \Zend_Reflection_Class
     */
    protected $_reflection;

    /**
     * Nazwa klasy
     * 
     * @var string
     */
    protected $_class;

    /**
     * Konstruktor
     * 
     * @param null|string $id
     * @param string $class
     * @param null|array|Zend_Config $options
     * @return void
     */
    public function __construct($id = null, $class, $options = null) {
        parent::__construct($id, $options);
        $this->_class = $class;
        $this->_reflection = new \Zend_Reflection_Class($class);
    }

    /**
     * Zapisuje wewnętrzne obiekty klasy przy serializacji zbioru danych
     * 
     * @return array
     */
    public function __sleep() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        if ($this->hasMaster() && is_object($this->_masterSource)) {
            $this->_masterSource = (string) $this->_masterSource->getId();
        }
        unset($this->_reflection);
        Msg::add($this->getId() . '-> koniec usypiania');
        return array_keys(get_object_vars($this));
    }

    /**
     * Wczytuje wewnętrzne obiekty klasy przy deserializacji zbioru danych
     * 
     * @return void
     */
    public function __wakeup() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        if ($this->hasMaster() && is_string($this->_masterSource)) {
            $this->_masterSource = \ZendY\Db\ActionManager::getInstance()->getDataSource($this->_masterSource);
        }
        $this->_reflection = new \Zend_Reflection_Class($this->_class);
    }

    /**
     * Zakaz używania metody setData
     * 
     * @param array $data
     * @throws Exception
     */
    final public function setData(array $data) {
        throw new Exception("You mustn't use method setData");
    }

    /**
     * Zwraca wszystkie stałe klasy w uporządkowanej tablicy
     * 
     * @return array
     */
    public function getData() {
        $constants = $this->_reflection->getConstants();
        $this->_data = array();
        foreach ($constants as $name => $value) {
            $this->_data[] = array(self::COL_NAME => $name, self::COL_VALUE => $value);
        }
        return $this->_data;
    }

    /**
     * Zwraca wszystkie zdefiniowane pola (kolumny) zbioru
     * 
     * @return array
     */
    public function getFields() {
        return array(self::COL_NAME, self::COL_VALUE);
    }

    /**
     * Tworzy pusty wiersz
     * 
     * @return array
     */
    protected function _createRow() {
        $row = array();
        foreach ($this->getFields() as $field) {
            $row[$field] = '';
        }
        return $row;
    }

}
