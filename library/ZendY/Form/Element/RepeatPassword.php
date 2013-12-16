<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Kontrolka powtórzonego hasła
 *
 * @author Piotr Zając
 */
class RepeatPassword extends Password {

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

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
     * Ustawia kontrolkę hasła, z którą ma być porównywana wartość
     * 
     * @param \ZendY\Form\Element\Password $control
     * @return \ZendY\Form\Element\RepeatPassword
     */
    public function setControl(Password $control) {
        $this->addValidator(new \Zend_Validate_Identical(array('token' => $control->getName())));
        return $this;
    }

}
