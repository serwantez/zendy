<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka listy rozwijalnej Combo
 *
 * @author Piotr Zając
 */
class Combobox extends CustomList {

    use \ZendY\ControlTrait;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'combobox';
        $this->setRegisterInArrayValidator(false);
        $this->addClasses(array(
            Css::COMBOBOX,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setFrontNaviParam('type', 'cb');
        $this->setFrontEditParam('type', 'cb');
    }

}
