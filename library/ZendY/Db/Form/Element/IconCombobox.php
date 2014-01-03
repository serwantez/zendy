<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Css;

/**
 * Bazodanowa kontrolka listy rozwijalnej z ikonami
 *
 * @author Piotr Zając
 */
class IconCombobox extends Combobox {

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
        $this->helper = 'iconCombobox';
        $this->addClasses(array(
            Css::ICONEDIT,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setRegisterInArrayValidator(false);
        $this->setFrontNaviParam('type', 'ic');
        $this->setFrontEditParam('type', 'ic');
    }

    /**
     * Ustawia klasę ikony rozwijania listy
     * 
     * @param string $icon
     * @return \ZendY\Db\Form\Element\IconCombobox
     */
    public function setIcon($icon) {
        $this->setJQueryParam(self::PARAM_ICON, $icon);
        return $this;
    }

    /**
     * Zwraca klasę ikony rozwijania listy
     * 
     * @return string
     */
    public function getIcon() {
        return $this->getJQueryParam(self::PARAM_ICON);
    }

}
