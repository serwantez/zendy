<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Container;

/**
 * Klasa kontenera harmonijkowego
 *
 * @author Piotr Zając
 */
class Accordion extends Panel {
    /**
     * Parametry jQuery UI
     */

    const PARAM_ACTIVE = "active";
    const PARAM_ANIMATE = "animate";
    const PARAM_COLLAPSIBLE = "collapsible";
    const PARAM_DISABLED = "disabled";
    const PARAM_EVENT = 'event';
    const PARAM_HEADER = 'header';
    const PARAM_HEIGHTSTYLE = 'heightStyle';
    const PARAM_ICONS = 'icons';


    /**
     * Parametr własny
     */
    const PARAM_HIDDENMAP = 'hiddenMap';

    /**
     * Wartości parametrów
     */
    const HEIGHTSTYLE_AUTO = "auto";
    const HEIGHTSTYLE_FILL = "fill";
    const HEIGHTSTYLE_CONTENT = "content";

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
     * @return \ZendY\Form\Container\Accordion
     */
    public function refreshDecorators() {
        $this->setJQueryParam(self::PARAM_HEIGHTSTYLE, self::HEIGHTSTYLE_FILL);
        $attribs = \ZendY\View\Helper\Widget::prepareCSS($this->getAttribs());
        $attribs['id'] = $this->getId();
        $attribs['jQueryParams'] = $this->getJQueryParams();
        $this->setDecorators(array(
            array('FormElements'),
            array('HtmlTag', array('tag' => 'div', 'id' => $this->getId())),
            array('AccordionContainer', $attribs)
        ));
        return $this;
    }

    /**
     * Ustawia parametr odpowiedzialny za odświeżenie map 
     * znajdujących się na domyślnie ukrytych zakładkach
     * 
     * @param string $hiddenPanelId
     * @param \ZendY\Form\Container\Base $container
     * @return \ZendY\Form\Container\Accordion
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
     * Dodaje panel do zakładki harmonijkowej
     * 
     * @param \ZendY\Form\Container\Base $container
     * @param string|null $name
     * @return \ZendY\Form\Container\Accordion
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
