<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Klasa bazowa przycisków
 *
 * @author Piotr Zając
 */
abstract class CustomButton extends Widget {

    /**
     * Parametry
     */
    const PARAM_SHORTKEY = 'shortkey';

    /**
     * Konstruktor
     *
     * @param  string|array|Zend_Config $spec Element name or configuration
     * @param  string|array|Zend_Config $options Element value or configuration
     * @return void
     */
    public function __construct($spec = null, $options = null) {
        if (is_string($spec) && ((null !== $options) && is_string($options))) {
            $options = array('label' => $options);
        }

        if (!isset($options['ignore'])) {
            $options['ignore'] = true;
        }

        parent::__construct($spec, $options);
    }

    /**
     * Zwraca etykietę przycisku
     * Jeśli nie ma etykiety, zwraca nazwę przycisku
     * Jeśli jest obecny tłumacz, zwraca przetłumaczoną etykietę.
     *
     * @return string
     */
    public function getLabel() {
        $value = parent::getLabel();

        if (null === $value) {
            $value = $this->getName();

            if (null !== ($translator = $this->getTranslator())) {
                return $translator->translate($value);
            }
        }

        return $value;
    }

    /**
     * Czy przycisk został zaznaczony?
     * 
     * @return bool
     */
    public function isChecked() {
        $value = $this->getValue();

        if (empty($value)) {
            return false;
        }
        if ($value != $this->getLabel()) {
            return false;
        }

        return true;
    }

    /**
     * Ładuje domyślne dekoratory
     * 
     * @return \ZendY\Form\Element\CustomButton
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->setDecorators(array(
                array('Tooltip'),
                array('UiWidgetElement'),
                array('Description', array('tag' => 'span', 'class' => 'description')),
                array('HtmlTag', array('tag' => 'div', 'style' => 'display: inline-block'))
            ))
            ;
        }
        return $this;
    }

    /**
     * Ładuje dekoratory
     * 
     * @return \ZendY\Form\Element\CustomButton
     */
    public function loadDecorators() {
        $this->setDecorators(array(
            array('Tooltip'),
            array('UiWidgetElement'),
            array('Description', array('tag' => 'span', 'class' => 'description')),
            array('HtmlTag', array('tag' => 'div', 'style' => 'display: inline-block'))
        ));
        return $this;
    }

    /**
     * Ustawia skrót klawiaturowy
     * 
     * @param string $shortKey
     * @return \ZendY\Form\Element\CustomButton
     */
    public function setShortKey($shortKey) {
        $this->setJQueryParam(self::PARAM_SHORTKEY, $shortKey);
        return $this;
    }

    /**
     * Zwraca skrót klawiaturowy
     * 
     * @return string 
     */
    public function getShortKey() {
        return $this->getJQueryParam(self::PARAM_SHORTKEY);
    }

    /**
     * Usuwa skrót klawiaturowy
     * 
     * @return \ZendY\Form\Element\CustomButton
     */
    public function removeShortKey() {
        $this->_removeJQueryParam(self::PARAM_SHORTKEY);
        return $this;
    }

}
