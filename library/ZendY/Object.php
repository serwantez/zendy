<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

/**
 * Klasa bazowa dla wszystkich obiektów
 *
 * @author Piotr Zając
 */
abstract class Object {

    /**
     * Identyfikator przyporządkowany do obiektu
     * 
     * @var string
     */
    protected $_name = null;

    /**
     * Licznik instancji
     * 
     * @var int 
     */
    static protected $_count = 0;

    /**
     * Konstruktor obiektu
     * 
     * @param string $id 
     * @return void
     */
    public function __construct($name = null) {
        self::$_count++;
        if (isset($name))
            $this->setName($name);
        else
            $this->setName($this->getClassName() . '_' . self::$_count);
    }

    /**
     * Filter a name to only allow valid variable characters
     *
     * @param  string $value
     * @param  bool $allowBrackets
     * @return string
     */
    public function filterName($value, $allowBrackets = false) {
        $charset = '^a-zA-Z0-9_\x7f-\xff-';
        if ($allowBrackets) {
            $charset .= '\[\]';
        }
        return preg_replace('/[' . $charset . ']/', '', (string) $value);
    }

    /**
     * Ustawia nazwę obiektu
     * 
     * @param string $name
     * @return \ZendY\Object 
     */
    public function setName($name) {
        $this->_name = $this->filterName($name);
        return $this;
    }

    /**
     * Zwraca nazwę obiektu
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Zwraca nazwę klasy obiektu
     * 
     * @return string 
     */
    public function getClassName() {
        return get_class($this);
    }

    /**
     * Kopiuje właściwości (pola i wartości) danego obiektu do siebie
     * 
     * @param \ZendY\Object $object 
     * @return void
     */
    public function cloneThis($object) {
        foreach (get_object_vars($object) as $key => $value) {
            $this->$key = $value;
        }
    }

}