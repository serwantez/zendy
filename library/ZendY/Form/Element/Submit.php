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
     * Właściwości komponentu
     */

    const PROPERTY_CAPTION = 'caption';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CAPTION,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_SHORTKEY,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_WIDTH
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'submit';
        $this->addClasses(array(
            Css::WIDGET,
            Css::CORNER_ALL,
            Css::SUBMIT
        ));
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setLabel($label, $width = null) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
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
