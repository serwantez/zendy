<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Kontrolka rejestracyjna do podania nazwy użytkownika.
 * Walidator ZendY_Validate_NoLoginExists sprawdza,
 * czy podany login już istnieje w bazie
 *
 * @author Piotr Zając
 */
class RegisterLogin extends Edit {

    /**
     * Opcje walidatora sprawdzającego istnienie loginu w bazie
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
     * Ustawia opcje walidatora sprawdzającego istnienie loginu w bazie
     * 
     * @param string $table
     * @param string $field
     * @return \ZendY\Form\Element\RegisterLogin
     */
    public function setTableAndField($table, $field) {
        $this->_validatorOptions = array(
            'table' => $table,
            'field' => $field
        );
        $this->addValidator(new \ZendY_Validate_NoLoginExists($this->_validatorOptions));

        return $this;
    }

    /**
     * Zwraca opcje walidatora sprawdzającego istnienie loginu w bazie
     * 
     * @return array
     */
    public function getTableAndField() {
        return $this->_validatorOptions;
    }

}
