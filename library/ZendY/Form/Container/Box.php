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
        $this->setClass(array(
            Css::DIALOG,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL,
            Css::FRONT,
            Css::DIALOG_BUTTONS
        ));
    }

    /**
     * Odświeża dekoratory po zmianach wykonanych na formularzu 
     * po dołaczeniu go do innego kontenera
     * 
     * @return \ZendY\Form\Container\Box
     */
    public function refreshDecorators() {
        $attribs = \ZendY\View\Helper\Widget::prepareCSS($this->getAttribs());
        $attribs['id'] = $this->getId();
        $attribs['jQueryParams'] = $this->getJQueryParams();
        $this->setDecorators(array(
            array('FormElements'),
            array(array('Inner' => 'HtmlTag'), array(
                    'class' => Css::PADDING_ALL . ' ' . Css::SCROLL_AUTO
            )),
            array('Box', $attribs)
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

