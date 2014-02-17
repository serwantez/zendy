<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Przycisk Submit z ikoną po prawej lub lewej stronie
 *
 * @author Piotr Zając
 */
class IconSubmit extends Submit {

    use \ZendY\ControlTrait;

    const PROPERTY_ICON = 'icon';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CAPTION,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_ICON,
        self::PROPERTY_NAME,
        self::PROPERTY_SHORTKEY,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_WIDTH
    );

    /**
     * Tablica ikony przycisku
     * primary - z lewej strony
     * secondary - z prawej strony
     * 
     * @var array
     */
    protected $_icon = array();

    /**
     * Ustawia klasę ikony przycisku
     * metoda sprawdzana dla ikon o wymiarach 16px x 16px
     * 
     * @param array|string $icon
     * @return \ZendY\Form\Element\IconSubmit
     */
    public function setIcon($icon) {
        if (!is_array($icon)) {
            $this->_icon = array('primary' => $icon);
        } else {
            $this->_icon = $icon;
        }

        if (array_key_exists('primary', $icon)) {
            $this->addClass(Css::SUBMIT_ICON_PRIMARY);
            $this->addClass($this->_icon['primary']);
        }

        if (array_key_exists('secondary', $icon)) {
            $this->addClass(Css::SUBMIT_ICON_SECONDARY);
            $this->addClass($this->_icon['secondary']);
        }

        return $this;
    }

    /**
     * Zwraca ikonę przycisku
     * 
     * @return array
     */
    public function getIcon() {
        return $this->_icon;
    }

}
