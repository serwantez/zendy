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

    use \ZendY\ControlTrait;

    const PARAM_MENU = 'menu';
    const PARAM_CONTEXT = 'context';
    const PARAM_DELEGATE = 'delegate';

    /**
     * Właściwości komponentu
     */
    const PROPERTY_CONTEXT = 'context';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_CLASSES,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_CONTEXT,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_MULTIOPTIONS,
        self::PROPERTY_NAME,
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
        $this->helper = 'contextMenu';
        $this->addClasses(array(
            Css::CONTEXTMENU,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setRegisterInArrayValidator(false);
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
    final public function setEmptyValue($empty = true) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setColumnSpace($space) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }   

    /**
     * Set element name
     * 
     * @param string $name
     * @return \ZendY\Form\Element\ContextMenu
     */
    public function setName($name) {
        parent::setName($name);
        $this->setJQueryParam(self::PARAM_MENU, '#' . $this->getName());
        return $this;
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
        $this->setJQueryParam(self::PARAM_CONTEXT, $contextElement->getName());
        return $this;
    }

    /**
     * Zwraca id powiązanej kontrolki
     * 
     * @return string
     */
    public function getContext() {
        return $this->getJQueryParam(self::PARAM_CONTEXT);
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
