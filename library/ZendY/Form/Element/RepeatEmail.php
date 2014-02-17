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
     * Właściwości komponentu
     */

    const PROPERTY_CONTROL = 'control';

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
        self::PROPERTY_CONTROL,
        self::PROPERTY_DISABLED,
        self::PROPERTY_LABEL,
        self::PROPERTY_ICON,
        self::PROPERTY_ICON_POSITION,
        self::PROPERTY_MAXLENGTH,
        self::PROPERTY_PLACEHOLDER,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Kontrolka adresu mailowego, z którą ma być porównywana wartość
     * 
     * @var ZendY\Form\Element\Email
     */
    protected $_control;

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

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
     * Ustawia kontrolkę adresu mailowego, z którą ma być porównywana wartość
     * 
     * @param \ZendY\Form\Element\Email $control
     * @return \ZendY\Form\Element\RepeatEmail
     */
    public function setControl(Email $control) {
        $this->_control = $control;
        $this->addValidator(new \Zend_Validate_Identical(array('token' => $control->getName())));
        return $this;
    }

    /**
     * Zwraca kontrolkę adresu mailowego, z którą ma być porównywana wartość
     * 
     * @return ZendY\Form\Element\Email
     */
    public function getControl() {
        return $this->_control;
    }

}
