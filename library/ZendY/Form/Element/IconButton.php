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
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->jQueryParams = array(
            self::PARAM_TEXT => false
        );
    }

    /**
     * Ustawia ikony przycisku
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
     * Zwraca ikony
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
