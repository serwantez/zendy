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
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
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

}
