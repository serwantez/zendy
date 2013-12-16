<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka adresu strony internetowej
 *
 * @author Piotr Zając
 */
class Url extends LinkEdit {

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
        parent::init();
        $this->setProtocol(self::PROTOCOL_HTTP);
        $this->addValidator(new \ZendY_Validate_IsUrl());
        $this->setWidth(160);
        $this->setIcon(Css::ICON_EXTLINK);
    }

}
