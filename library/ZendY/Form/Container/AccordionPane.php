<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Container;

/**
 * Klasa panelu zakładki harmonijkowej
 *
 * @author Piotr Zając
 */
class AccordionPane extends Panel {

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
     * Ustawia identyfikator kontenera zakładki
     * 
     * @param string $containerId
     * @return \ZendY\Form\Container\AccordionPane
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
     * @return \ZendY\Form\Container\AccordionPane
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
     * @return \ZendY\Form\Container\AccordionPane
     */
    public function refreshDecorators() {
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'div')),
            array('AccordionPane', array('jQueryParams' => array(
                        'id' => $this->getId(),
                        'containerId' => $this->_containerId,
                        'title' => $this->_title
                )))
        ));
        return $this;
    }

}
