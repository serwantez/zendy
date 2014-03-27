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
     * Właściwości komponentu
     */

    const PROPERTY_MAXLENGTH = 'maxLength';
    const PROPERTY_PLACEHOLDER = 'placeHolder';

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
        self::PROPERTY_INLINE,
        self::PROPERTY_LABEL,
        self::PROPERTY_MAXLENGTH,
        self::PROPERTY_NAME,
        self::PROPERTY_PLACEHOLDER,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'edit';
        $this->addClasses(array(
            Css::EDIT,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setWidth(150);
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

    /**
     * Zwraca maksymalną liczbę wprowadzanych znaków (tylko przeglądarki obsługujące HTML5)
     * 
     * @return int
     */
    public function getMaxLength() {
        return $this->getAttrib('maxlength');
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
     * Zwraca treść podpowiedzi w polu (tylko przeglądarki obsługujące HTML5)
     * 
     * @return string
     */
    public function getPlaceHolder() {
        return $this->getAttrib('placeholder');
    }

}
