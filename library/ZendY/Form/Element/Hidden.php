<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Kontrolka pola ukrytego
 *
 * @author Piotr Zając
 */
class Hidden extends Widget {

    use \ZendY\ControlTrait;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'formHidden';
    }

}
