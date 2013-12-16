<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Kontrolka powtórzonego adresu mailowego
 *
 * @author Piotr Zając
 */
class RepeatEmail extends Email {

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
     * Ustawia kontrolkę adresu mailowego, z którą ma być porównywana wartość
     * 
     * @param \ZendY\Form\Element\Email $control
     * @return \ZendY\Form\Element\RepeatEmail
     */
    public function setControl(Email $control) {
        $this->addValidator(new \Zend_Validate_Identical(array('token' => $control->getName())));
        return $this;
    }

}
