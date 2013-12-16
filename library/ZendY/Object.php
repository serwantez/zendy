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
    protected $_id = null;

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
    public function __construct($id = null) {
        self::$_count++;
        if (isset($id))
            $this->setId($id);
        else
            $this->setId($this->getClassName() . '_' . self::$_count);
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
     * Ustawia identyfikator obiektu
     * 
     * @param string $id
     * @return \ZendY\Object 
     */
    public function setId($id) {
        $this->_id = $this->filterName($id);
        return $this;
    }

    /**
     * Zwraca identyfikator obiektu
     * 
     * @return string
     */
    public function getId() {
        return $this->_id;
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