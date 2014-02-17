<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Container;

use ZendY\Css;

/**
 * Klasa panelu - kontenera
 *
 * @author Piotr Zając
 */
class Box extends Base {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_TITLE = 'title';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_SPACE,
        self::PROPERTY_TITLE,
        self::PROPERTY_WIDGETCLASS,
        self::PROPERTY_WIDTH
    );

    /**
     * Licznik instancji
     *
     * @var int
     */
    protected static $count = 0;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefault() {
        parent::_setDefault();
        $this->setTitle($this->getId());
    }

    /**
     * Odświeża dekoratory po zmianach wykonanych na formularzu 
     * po dołaczeniu go do innego kontenera
     * 
     * @return \ZendY\Form\Container\Box
     */
    public function refreshDecorators() {
        $attribs = \ZendY\View\Helper\Widget::prepareCSS($this->getAttribs());
        $boxAttribs['id'] = $this->getName();
        $boxAttribs['jQueryParams'] = $this->getJQueryParams();
        $boxAttribs['class'] = array(
            Css::BOX,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL,
            Css::ALIGN_CLIENT
        );
        unset($attribs['id']);
        unset($attribs['name']);

        $space = $this->_space['value'] . $this->_space['unit'];

        $this->setDecorators(array(
            array('FormElements'),
            array(array('Inner' => 'HtmlTag'), array(
                    'class' => Css::PADDING_ALL . ' ' . Css::SCROLL_AUTO
            )),
            array('Box', $boxAttribs),
            array(array('Space' => 'HtmlTag'), array(
                    'style' => sprintf('position: absolute; left: %s; top: %s; right: %s; bottom: %s;'
                            , $space
                            , $space
                            , $space
                            , $space)
            )),
            array(array('Outer' => 'HtmlTag'), $attribs)
        ));
        return $this;
    }

    /**
     * Ustawia tytuł dla części nagłówkowej
     * 
     * @param string $title
     * @return \ZendY\Form\Container\Box
     */
    public function setTitle($title) {
        $this->setJQueryParam('title', $title);
        return $this;
    }

    /**
     * Zwraca tytuł dla części nagłówkowej
     *
     * @return string
     */
    public function getTitle() {
        return $this->getJQueryParam('title');
    }

    /**
     * Obsługa zdarzenia dołączenia panelu do formularza nadrzędnego
     * 
     * @return \ZendY\Form\Container\Box
     */
    public function onContain() {
        $this->removeAttrib('method');
        $this->refreshDecorators();
        return $this;
    }

}

