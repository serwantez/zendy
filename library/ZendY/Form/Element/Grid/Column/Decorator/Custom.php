<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element\Grid\Column\Decorator;

use ZendY\Form\Element\Grid\Column;

/**
 * Klasa bazowa dekoratorów kolumn grida
 * 
 * @author Piotr Zając
 */
abstract class Custom extends Column {

    /**
     * Opcje dekoratora
     * 
     * @var array
     */
    protected $_options = array();

    /**
     * Obiekt kolumny
     * 
     * @var ZendY\Form\Element\Grid\Column
     */
    protected $_column;

    public function __construct(Column $column, array $options = array()) {
        $this->_column = $column;
        $this->_options = $options;
        parent::__construct($column->getName(), $column->getAttribs());
        $this->decorate();
    }

    /**
     * Zwraca nazwę pola kolumny
     * 
     * @return string
     */
    public function getName() {
        return $this->_column->getName();
    }

    /**
     * Zastępuje ustawianie atrybutów kolumny
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value) {
        $this->_column->setAttrib($name, $value);
    }

    /**
     * Zastępuje uzyskiwanie dostępu do atrybutów kolumny
     * 
     * @param string $name nazwa atrybutu kolumny
     * @return mixed
     */
    public function __get($name) {
        return $this->_column->getAttrib($name);
    }

    /**
     * Zwraca pojedynczy atrybut kolumny
     * 
     * @param string $name nazwa atrybutu kolumny
     * @return mixed
     */
    public function getAttrib($name) {
        return $this->_column->getAttrib($name);
    }

    /**
     * Ustawia pojedynczy atrybut kolumny
     * 
     * @param string $name
     * @param mixed $value
     * @return \ZendY\Form\Element\Grid\Column\Decorator\Custom
     */
    public function setAttrib($name, $value) {
        $this->_column->getAttrib($name, $value);
        return $this;
    }

    /**
     * Zwraca wszystkie atrybuty kolumny
     * 
     * @return array
     */
    public function getAttribs() {
        return $this->_column->getAttribs();
    }

    /**
     * Zwraca wartość komórki kolumny
     * 
     * @param array $row wiersz zawierający wartości komórek
     * @return mixed
     */
    public function cellValue(array $row) {
        return $this->_column->cellValue($row);
    }

    /*
     * Dekoruje kolumnę
     * 
     * @return void
     */

    abstract public function decorate();
}

