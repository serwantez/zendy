<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Klasa bazowa dla tekstowych kontrolek edycyjnych
 *
 * @author Piotr Zając
 */
abstract class CustomEdit extends Widget {

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'edit';
        $this->addClasses(array(
            Css::EDIT,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
    }

    /**
     * Ustawia podpowiedź w polu, ukrywaną przy uzyskaniu fokusu (tylko przeglądarki obsługujące HTML5)
     * 
     * @param string $placeHolder
     * @return \ZendY\Form\Element\CustomEdit
     */
    public function setPlaceHolder($placeHolder) {
        $this->setAttrib('placeholder', $placeHolder);
        return $this;
    }

    /**
     * Ogranicza liczbę wprowadzanych znaków (tylko przeglądarki obsługujące HTML5)
     * 
     * @param int $maxlength
     * @return \ZendY\Form\Element\CustomEdit
     */
    public function setMaxLength($maxlength) {
        $this->setAttrib('maxlength', $maxlength);
        return $this;
    }

}
