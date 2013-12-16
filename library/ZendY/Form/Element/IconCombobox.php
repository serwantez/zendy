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
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'iconcombobox';
        $this->addClasses(array(
            Css::ICONEDIT,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));        
        $this->setRegisterInArrayValidator(false);
    }

    /**
     * Ustawia klasę ikony
     * 
     * @param string $icon
     * @return \ZendY\Form\Element\IconCombobox
     */
    public function setIcon($icon) {
        $this->jQueryParams[self::PARAM_ICON] = $icon;
        return $this;
    }

    /**
     * Zwraca klasę ikony
     * 
     * @return string
     */
    public function getIcon() {
        return $this->jQueryParams[self::PARAM_ICON];
    }

}
