<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Container;

/**
 * Klasa kontenera zakładek
 *
 * @author Piotr Zając
 */
class Tab extends Panel {

    const PARAM_HIDDENMAP = 'hiddenMap';

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Odświeża dekoratory po zmianach wykonanych na formularzu 
     * po dołaczeniu go do innego kontenera
     * 
     * @return \ZendY\Form\Container\Tab
     */
    public function refreshDecorators() {
        $attribs = \ZendY\View\Helper\Widget::prepareCSS($this->getAttribs());
        $tabAttribs['id'] = $this->getId();
        $tabAttribs['jQueryParams'] = $this->getJQueryParams();
        $this->setDecorators(array(
            array('FormElements'),
            array(array('Inner' => 'HtmlTag'), array(
                    'tag' => 'div',
                    'id' => $this->getId()
            )),
            array('TabContainer', $tabAttribs),
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
     * Ustawia parametr odpowiedzialny za odświeżenie map 
     * znajdujących się na domyślnie ukrytych zakładkach
     * 
     * @param string $hiddenPanelId
     * @param \ZendY\Form\Container\Base $container
     * @return \ZendY\Form\Container\Tab
     */
    public function refreshHiddenMap($hiddenPanelId, Base $container) {
        $elements = $container->getElements();
        foreach ($elements as $element) {
            if ($element instanceof \ZendY\Form\Element\CustomMap) {
                $this->jQueryParams[self::PARAM_HIDDENMAP][] = array(
                    'panel' => $hiddenPanelId,
                    'map' => $element->getId()
                );
            }
        }
        foreach ($container->getSubForms() as $subform) {
            $this->refreshHiddenMap($hiddenPanelId, $subform);
        }
        return $this;
    }

    /**
     * Dodaje panel do zakładki
     * 
     * @param \ZendY\Form\Container\Base $container
     * @param string|null $name
     * @return \ZendY\Form\Container\Tab
     */
    public function addContainer(Base $container, $name = null) {
        $container->setContainerId($this->getId());
        $number = count($this->getSubForms()) + 1;
        $container->setName($this->getId() . '_Panel_' . $number);

        if (strlen($container->getTitle()) == 0) {
            $container->setTitle($container->getId());
        }
        $this->refreshHiddenMap($container->getId(), $container);
        parent::addContainer($container, $name);
        return $this;
    }

}
