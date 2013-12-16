<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka menu podręcznego
 *
 * @author Piotr Zając
 */
class ContextMenu extends CustomList {

    const PARAM_MENU = 'menu';
    const PARAM_CONTEXT = 'context';
    const PARAM_DELEGATE = 'delegate';

    use \ZendY\ControlTrait;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'contextMenu';
        $this->addClasses(array(
            Css::CONTEXTMENU,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));        
        $this->setRegisterInArrayValidator(false);
        $this->setJQueryParam(self::PARAM_MENU, '#' . $this->getId());
    }

    /**
     * Ładuje domyślne dekoratory
     *
     * @return void
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->setDecorators(array(
                array('UiWidgetMultiElement')
            ))
            ;
        }
    }

    /**
     * Ładuje dekoratory
     * 
     * @return void
     */
    public function loadDecorators() {
        $this->_labelOptions['id'] = $this->getName();
        $this->setDecorators(array(
            array('UiWidgetMultiElement')
        ));
    }

    /**
     * Ustawia powiązaną kontrolkę
     * 
     * @param \ZendY\Form\Element\Widget $contextElement
     * @return \ZendY\Form\Element\ContextMenu
     */
    public function setContext(\ZendY\Form\Element\Widget $contextElement) {
        $this->setJQueryParam(self::PARAM_CONTEXT, $contextElement->getId());
        return $this;
    }

    /**
     * Przydziela menu do elementu strony (ustawia "delegata")
     * 
     * @param string $delegate
     * @return \ZendY\Form\Element\ContextMenu
     */
    public function setDelegate($delegate) {
        $this->setJQueryParam(self::PARAM_DELEGATE, $delegate);
        return $this;
    }
    
}
