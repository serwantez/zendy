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
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'formHidden';
    }

}
