<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Przycisk z ikoną
 *
 * @author Piotr Zając
 */
class IconButton extends Button {
    /**
     * Parametry
     */

    const PARAM_ICONS = 'icons';

    /**
     * Właściwości komponentu
     */
    const PROPERTY_ICONS = 'icons';
    const PROPERTY_VISIBLETEXT = 'visibleText';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_ICONS,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
        self::PROPERTY_SHORTKEY,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VISIBLETEXT,
        self::PROPERTY_WIDTH
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->setVisibleText(false);
    }

    /**
     * Ustawia klasy ikon
     * 
     * @param array|string $icons
     * @return \ZendY\Form\Element\IconButton
     */
    public function setIcons($icons) {
        if (!is_array($icons)) {
            $icons = array('primary' => $icons);
        }
        $this->setJQueryParam(self::PARAM_ICONS, $icons);

        return $this;
    }

    /**
     * Zwraca klasy ikon
     * 
     * @return array
     */
    public function getIcons() {
        return $this->getJQueryParam(self::PARAM_ICONS);
    }

    /**
     * Czy ma się wyświetlać tekst na przycisku?
     * 
     * @param bool $visible
     * @return \ZendY\Form\Element\IconButton
     */
    public function setVisibleText($visible) {
        $this->setJQueryParam(self::PARAM_TEXT, $visible);
        return $this;
    }

    /**
     * Zwraca informację o wyświetlaniu tekstu na przyciskach
     * 
     * @return bool
     */
    public function getVisibleText() {
        return $this->getJQueryParam(self::PARAM_TEXT);
    }

}
