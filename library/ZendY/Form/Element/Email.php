<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka adresu poczty elektronicznej
 *
 * @author Piotr Zając
 */
class Email extends LinkEdit {

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
        $this->setProtocol(self::PROTOCOL_MAIL);
        $this->addValidator(new \Zend_Validate_EmailAddress());
        $this->setWidth(160);
        $this->setIcon(Css::ICON_MAILCLOSED);
    }

}
