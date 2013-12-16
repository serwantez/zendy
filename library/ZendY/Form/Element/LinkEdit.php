<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Kontrolka edycyjna zawierająca dynamiczny link
 *
 * @author Piotr Zając
 */
class LinkEdit extends IconEdit {
    /**
     * Parametry
     */

    const PARAM_PROTOCOL = 'protocol';

    /**
     * Protokoły linków
     */
    const PROTOCOL_HTTP = '';
    const PROTOCOL_MAIL = 'mailto:';
    const PROTOCOL_SKYPE = 'skype:';

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Inicjalizuje obiekt
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->helper = 'linkEdit';
    }

    /**
     * Ustawia nazwę protokołu dla linku
     * 
     * @param string $protocol
     * @return \ZendY\Form\Element\LinkEdit
     */
    public function setProtocol($protocol) {
        $this->setJQueryParam(self::PARAM_PROTOCOL, $protocol);
        return $this;
    }

    /**
     * Zwraca nazwę protokołu dla linku
     * 
     * @return string
     */
    public function getProtocol() {
        return $this->getJQueryParam(self::PARAM_PROTOCOL);
    }

}
