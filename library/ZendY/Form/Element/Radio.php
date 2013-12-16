<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka typu radio
 *
 * @author Piotr Zając
 */
class Radio extends CustomList {

    use \ZendY\ControlTrait;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'radio';
        $this->setRegisterInArrayValidator(false);
        $this->setSeparator('  ');
        $this->addClasses(array(Css::RADIO));
        $this->setFrontNaviParam('type', 'rd');
        $this->setFrontEditParam('type', 'rd');
        parent::init();
    }

}
