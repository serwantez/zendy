<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;
use ZendY\Exception;

/**
 * Kontrolka adresu strony internetowej
 *
 * @author Piotr Zając
 */
class Url extends LinkEdit {
    
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
        $this->setJQueryParam(self::PARAM_PROTOCOL, self::PROTOCOL_HTTP);
        $this->addValidator(new \ZendY_Validate_IsUrl());
        $this->setWidth(160);
        $this->setIcon(Css::ICON_EXTLINK);
    }
    
    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setProtocol($protocol) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }    

}
