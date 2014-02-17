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
     * @var ZendY\Form\Element\Password
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
     * Ustawia kontrolkę hasła, z którą ma być porównywana wartość
     * 
     * @param \ZendY\Form\Element\Password $control
     * @return \ZendY\Form\Element\RepeatPassword
     */
    public function setControl(Password $control) {
        $this->_control = $control;
        $this->addValidator(new \Zend_Validate_Identical(array('token' => $control->getName())));
        return $this;
    }
    
    /**
     * Zwraca kontrolkę hasła, z którą ma być porównywana wartość
     * 
     * @return ZendY\Form\Element\Password
     */
    public function getControl() {
        return $this->_control;
    }    

}
