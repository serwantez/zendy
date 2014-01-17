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
     * Licznik instancji
     *
     * @var int
     */
    protected static $count = 0;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
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
        $boxAttribs['id'] = $this->getId();
        $boxAttribs['jQueryParams'] = $this->getJQueryParams();
        $boxAttribs['class'] = array(
            Css::DIALOG,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL,
            Css::FRONT,
            Css::DIALOG_BUTTONS,
            Css::ALIGN_CLIENT
        );
        unset($attribs['id']);
        unset($attribs['name']);
        $this->setDecorators(array(
            array('FormElements'),
            array(array('Inner' => 'HtmlTag'), array(
                    'class' => Css::PADDING_ALL . ' ' . Css::SCROLL_AUTO
            )),
            array('Box', $boxAttribs),
            array(array('Space' => 'HtmlTag'), array(
                    'style' => sprintf('position: absolute; left: %s; top: %s; right: %s; bottom: %s;'
                            , $this->_space . 'px'
                            , $this->_space . 'px'
                            , $this->_space . 'px'
                            , $this->_space . 'px')
            )),
            array(array('Outer' => 'HtmlTag'), $attribs)
        ));
        return $this;
    }

    /**
     * Ustawia tytuł okna
     * 
     * @param string $title
     * @return \ZendY\Form\Container\Box
     */
    public function setTitle($title) {
        $this->setJQueryParam('title', $title);
        return $this;
    }

    /**
     * Zwraca tytuł okna
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

