<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Container;

use ZendY\Exception;

/**
 * Klasa panelu zakładki
 *
 * @author Piotr Zając
 */
class TabPane extends Panel {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_TITLE = 'title';

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Identyfikator kontenera zakładki
     * 
     * @var string 
     */
    protected $_containerId;

    /**
     * Tytuł zakładki
     * 
     * @var string
     */
    protected $_title = '';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_CLASSES,
        self::PROPERTY_NAME,
        self::PROPERTY_TITLE,
    );

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setHeight($value, $unit = 'px') {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setWidth($value, $unit = 'px') {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setAlign($align) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setWidgetClass($widgetClass) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setSpace($space = 2) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Ustawia identyfikator kontenera zakładki
     * 
     * @param string $containerId
     * @return \ZendY\Form\Container\TabPane
     */
    public function setContainerId($containerId) {
        $this->_containerId = $containerId;
        return $this;
    }

    /**
     * Zwraca identyfikator kontenera zakładki
     * 
     * @return string
     */
    public function getContainerId() {
        return $this->_containerId;
    }

    /**
     * Ustawia tytuł zakładki
     * 
     * @param string $title
     * @return \ZendY\Form\Container\TabPane
     */
    public function setTitle($title) {
        $this->_title = $title;
        return $this;
    }

    /**
     * Zwraca tytuł zakładki
     * 
     * @return string
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * Odświeża dekoratory po zmianach wykonanych na formularzu 
     * po dołaczeniu go do innego kontenera
     * 
     * @return \ZendY\Form\Container\TabPane
     */
    public function refreshDecorators() {
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'div')),
            array('TabPane', array('jQueryParams' => array(
                        'id' => $this->getName(),
                        'containerId' => $this->_containerId,
                        'title' => $this->_title
                )))
        ));
        return $this;
    }

}
