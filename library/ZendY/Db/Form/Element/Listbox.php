<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Css;

/**
 * Bazodanowa kontrolka listy rozwiniętej
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
        $this->setRegisterInArrayValidator(false);
        $this->addClasses(array(
            Css::LISTBOX,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setFrontNaviParam('type', 'lb');
        $this->setFrontEditParam('type', 'lb');
    }

}
