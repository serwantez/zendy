<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka rozwiniętej listy
 *
 * @author Piotr Zając
 */
class Listbox extends Combobox {
    
    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_COLUMNSPACE,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_DISABLED,
        self::PROPERTY_EMPTYVALUE,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_MULTIOPTIONS,
        self::PROPERTY_NAME,
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
        $this->setWidth(150);
        $this->setHeight(50);        
        $this->helper = 'listbox';
        $this->addClasses(array(
            Css::LISTBOX,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setRegisterInArrayValidator(false);
    }

}
