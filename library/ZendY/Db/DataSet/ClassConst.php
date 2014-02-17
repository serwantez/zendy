<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Msg;
use ZendY\Exception;

/**
 * Zbiór stałych klasy
 *
 * @author Piotr Zając
 */
class ClassConst extends ArraySet {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_CLASS = 'class';

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
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_CLASS,
        self::PROPERTY_MASTER,
        self::PROPERTY_NAME
    );

    /**
     * Ustawia wartości domyślne
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->_primary = array(1 => self::COL_VALUE);
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
     * Ustawia nazwę klasy
     * 
     * @param string $class
     * @return \ZendY\Db\DataSet\ClassConst
     */
    public function setClass($class) {
        $this->_class = (string) $class;
        $this->_reflection = new \Zend_Reflection_Class($class);
        return $this;
    }

    /**
     * Zwraca nazwę klasy
     * 
     * @return string
     */
    public function getClass() {
        return $this->_class;
    }

    /**
     * Zakaz używania metody
     * 
     * @param array $data
     * @throws Exception
     */
    final public function setData(array $data) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param array $primary
     * @throws Exception
     */
    final public function setPrimary($primary) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
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
    public function getColumns() {
        return array(self::COL_NAME, self::COL_VALUE);
    }

    /**
     * Tworzy pusty wiersz
     * 
     * @return array
     */
    protected function _createRow() {
        $row = array();
        foreach ($this->getColumns() as $field) {
            $row[$field] = '';
        }
        return $row;
    }

}
