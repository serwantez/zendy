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
     * Właściwości komponentu
     */
    const PROPERTY_PROTOCOL = 'protocol';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_LABEL,
        self::PROPERTY_ICON,
        self::PROPERTY_ICON_POSITION,
        self::PROPERTY_MAXLENGTH,
        self::PROPERTY_PLACEHOLDER,
        self::PROPERTY_PROTOCOL,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
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
