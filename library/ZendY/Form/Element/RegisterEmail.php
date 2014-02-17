<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Kontrolka rejestracyjna do podania adresu poczty e-mail.
 * Walidator ZendY_Validate_NoEmailExists sprawdza, 
 * czy podany e-mail już istnieje w bazie
 *
 * @author Piotr Zając
 */
class RegisterEmail extends Email {
    /**
     * Własciwości komponentu
     */

    const PROPERTY_FIELD = 'field';
    const PROPERTY_TABLE = 'table';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_FIELD,
        self::PROPERTY_LABEL,
        self::PROPERTY_ICON,
        self::PROPERTY_ICON_POSITION,
        self::PROPERTY_MAXLENGTH,
        self::PROPERTY_PLACEHOLDER,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TABLE,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Opcje walidatora sprawdzającego istnienie adresu w bazie
     * 
     * @var array
     */
    protected $_validatorOptions;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->setRequired(true);
    }

    /**
     * Ustawia opcje walidatora sprawdzającego istnienie adresu w bazie
     * 
     * @param string $table
     * @param string $field
     * @return \ZendY\Form\Element\RegisterEmail
     */
    public function setTableAndField($table, $field) {
        $this->_validatorOptions = array(
            'table' => $table,
            'field' => $field
        );
        $this->addValidator(new \ZendY_Validate_NoEmailExists($this->_validatorOptions));

        return $this;
    }

    /**
     * Zwraca opcje walidatora sprawdzającego istnienie adresu w bazie
     * 
     * @return array
     */
    public function getTableAndField() {
        return $this->_validatorOptions;
    }

    /**
     * Ustawia tabelę walidatora
     * 
     * @param string $table
     * @return \ZendY\Form\Element\RegisterEmail
     */
    public function setTable($table) {
        $this->_validatorOptions['table'] = $table;
        return $this;
    }

    /**
     * Zwraca tabelę walidatora
     * 
     * @return string
     */
    public function getTable() {
        return $this->_validatorOptions['table'];
    }

    /**
     * Ustawia kolumnę w tabeli walidatora
     * 
     * @param string $field
     * @return \ZendY\Form\Element\RegisterEmail
     */
    public function setField($field) {
        $this->_validatorOptions['field'] = $field;
        return $this;
    }

    /**
     * Zwraca kolumnę w tabeli walidatora
     * 
     * @return string
     */
    public function getField() {
        return $this->_validatorOptions['field'];
    }

}
