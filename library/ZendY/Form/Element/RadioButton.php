<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Przyciski "radio"
 *
 * @author Piotr Zając
 */
class RadioButton extends Radio {
    /**
     * Parametry
     */

    const PARAM_TEXT = 'text';
    const PARAM_ICONS = 'icons';

    /**
     * Właściwości komponentu
     */
    const PROPERTY_ICONS = 'icons';
    const PROPERTY_TOOLBUTTON = 'toolButton';
    const PROPERTY_VISIBLETEXT = 'visibleText';

    /**
     * Tablica właściwości
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_COLUMNSPACE,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_ICONS,
        self::PROPERTY_LABEL,
        self::PROPERTY_MULTIOPTIONS,
        self::PROPERTY_NAME,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLBUTTON,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_VISIBLETEXT,
        self::PROPERTY_WIDTH
    );

    /**
     * Czy jest przycisk paska narzędziowego
     * 
     * @var bool
     */
    protected $isToolButton = false;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'radioButton';
        $this->setSeparator('');
        $this->setVisibleText(true);
    }

    /**
     * Ustawia ikony
     * 
     * @param array|string $icons
     * @return \ZendY\Form\Element\RadioButton
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
     * Ustawia wyświetlanie tekstu na przycisku
     * 
     * @param bool $visible
     * @return \ZendY\Form\Element\RadioButton
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

    /**
     * Ustawia wygląd przycisków paska narzędziowego
     * 
     * @param bool $isToolButton
     * @return \ZendY\Form\Element\RadioButton
     */
    public function setToolButton($isToolButton) {
        $this->isToolButton = $isToolButton;
        $this->loadDecorators();
        return $this;
    }

    /**
     * Zwraca informację, czy przyciski mają wyglądać jak przyciski paska narzędziowego
     * @return bool
     */
    public function getToolButton() {
        return $this->isToolButton;
    }

    /**
     * Ładuje dekoratory
     *
     * @return \ZendY\Form\Element\RadioButton
     */
    public function loadDecorators() {
        if ($this->isToolButton) {
            //Ładuje dekoratory właściwe dla paska narzędziowego
            $this->setDecorators(array(
                array('UiWidgetMultiElement'),
                array('HtmlTag', array(
                        'tag' => 'div',
                        'id' => $this->getName() . '-container',
                        'style' => 'display: inline-block'
                ))
            ))
            ;
        } else {
            //parent::loadDecorators();
            $this->_labelOptions['id'] = $this->getName() . '_label';
            $this->setDecorators(array(
                array('UiWidgetMultiElement'),
                array('Errors', array(
                        'tag' => 'ul',
                        'class' => Css::WIDGET . ' ' . Css::STATE_ERROR . ' ' . Css::CORNER_ALL . ' ' . Css::INVISIBLE
                )),
                array('Description', array('tag' => 'span', 'class' => 'field-description')),
                array('Label', $this->_labelOptions),
                array(array('Section' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field-container'))
            ));
        }
        return $this;
    }

}
