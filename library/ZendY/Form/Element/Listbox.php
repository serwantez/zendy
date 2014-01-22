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
     * Licznik instancji
     * 
     * @var int 
     */
    static protected $count = 0;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'listbox';
        $this->addClasses(array(
            Css::LISTBOX,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setWidth(150);
        $this->setHeight(50);
        $this->setRegisterInArrayValidator(false);
    }

}
