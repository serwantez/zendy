<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Przycisk wysyłający formularz
 *
 * @author Piotr Zając
 */
class Submit extends CustomButton {

    use \ZendY\ControlTrait;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->helper = 'submit';
        $this->addClasses(array(
            Css::WIDGET,
            Css::CORNER_ALL,
            Css::SUBMIT
        ));
    }

    /**
     * Ustawia tekst przycisku.
     * Tekstem przycisku Submit jest jego wartość (atrybut value)
     * 
     * @param string $label
     * @return \ZendY\Form\Element\Submit
     */
    public function setCaption($label) {
        $this->setValue($label);
        return $this;
    }

    /**
     * Zwraca tekst przycisku
     * 
     * @return string
     */
    public function getCaption() {
        return $this->getValue();
    }

    /**
     * Zwraca przetłumaczoną wartość przycisku submit
     * 
     * @return string
     */
    public function getValue() {
        $translator = $this->getTranslator();
        if (null !== $translator) {
            return $translator->translate($this->_value);
        }

        return $this->_value;
    }

}
