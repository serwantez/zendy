<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Kontrolka tekstowa z hasłem
 *
 * @author Piotr Zając
 */
class IconPassword extends IconEdit {

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->helper = 'iconPassword';
        $this->setIcon(\ZendY\Css::ICON_KEY);
    }

}
