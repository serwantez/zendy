<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

/**
 * Przycisk
 *
 * @author Piotr Zając
 */
class Button extends CustomButton {

    use \ZendY\ControlTrait;

    /**
     * Parametry
     */

    const PARAM_DISABLED = 'disabled';
    const PARAM_LABEL = 'label';
    const PARAM_TEXT = 'text';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->helper = 'button';
    }

    /**
     * Ustawia etykietę przycisku
     * 
     * @param string $label
     * @param null $width zmienna występuje ze względu na kompatybilność
     * z metodą rodzica
     * @return \ZendY\Form\Element\Button
     */
    public function setLabel($label, $width = null) {
        if (null !== ($translator = $this->getTranslator())) {
            $this->setJQueryParam(self::PARAM_LABEL, $translator->translate($label));
        } else
            $this->setJQueryParam(self::PARAM_LABEL, $label);
        return $this;
    }

    /**
     * Zwraca etykietę przycisku
     * 
     * @return string
     */
    public function getLabel() {
        return $this->getJQueryParam(self::PARAM_LABEL);
    }

}
