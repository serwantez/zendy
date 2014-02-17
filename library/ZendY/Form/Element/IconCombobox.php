<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka rozwijalnej listy z ikonami
 *
 * @author Piotr Zając
 */
class IconCombobox extends Combobox {
    /**
     * Parametry
     */

    const PARAM_ICON = 'icon';

    /**
     * Właściwości komponentu
     */
    const PROPERTY_ICON = 'icon';

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
        self::PROPERTY_ICON,
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
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'iconCombobox';
        $this->addClasses(array(
            Css::ICONEDIT,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setRegisterInArrayValidator(false);
    }

    /**
     * Ustawia klasę ikony rozwijania listy
     * 
     * @param string $icon
     * @return \ZendY\Form\Element\IconCombobox
     */
    public function setIcon($icon) {
        $this->setJQueryParam(self::PARAM_ICON, $icon);
        return $this;
    }

    /**
     * Zwraca klasę ikony akcji rozwijania listy
     * 
     * @return string
     */
    public function getIcon() {
        return $this->getJQueryParam(self::PARAM_ICON);
    }

}
