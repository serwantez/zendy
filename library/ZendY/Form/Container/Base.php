<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Container;

use ZendY\Css;
use ZendY\JQuery;
use ZendY\Db\Form;
use ZendY\Exception;

/**
 * Klasa bazowa dla kontenerów formularza
 *
 * @author Piotr Zając
 */
abstract class Base extends Form {

    use \ZendY\JQueryTrait;

    /**
     * Właściwości komponentu
     */

    const PROPERTY_WIDGETCLASS = 'widgetClass';

    /**
     * Licznik instancji
     *
     * @var int
     */
    protected static $count = 0;

    /**
     * Tablica nazw zdarzeń
     *
     * @var array
     */
    protected $_events = array();

    /**
     * Klasa wewnętrznego dekoratora
     * 
     * @var string
     */
    protected $_widgetClass;

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
        self::PROPERTY_WIDGETCLASS,
        self::PROPERTY_WIDTH
    );

    /**
     * Konstruktor
     *
     * @param string|null $id
     * @param array|Zend_Config|null $options
     * @return void
     */
    public function __construct($options = null) {
        if (isset($options['id'])) {
            $this->setName($options['id']);
            unset($options['id']);
        } else {
            $this::$count++;
            $this->setName(get_class($this) . '_' . $this::$count);
        }
        parent::__construct($options);
        $this->removeDecorator('Form');
    }

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->_widgetClass = Css::WIDGET;
        $this->setIsSubForm(true);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setAction($action) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setAjaxValidator($ajaxValidator = true) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Ustawia klasę wewnętrznego dekoratora
     * 
     * @param string $widgetClass
     * @return \ZendY\Form\Container\Base
     */
    public function setWidgetClass($widgetClass) {
        $this->_widgetClass = $widgetClass;
        return $this;
    }

    /**
     * Zwraca klasę wewnętrznego dekoratora
     * 
     * @return string
     */
    public function getWidgetClass() {
        return $this->_widgetClass;
    }

    /**
     * Tworzy zmienne tablicowe z parametrów zdarzeń
     * 
     * @return \ZendY\Form\Container\Custom
     */
    protected function _prepareEventParams() {
        //parametry przechowujące kody zdarzeń muszą być tablicami
        foreach ($this->_events as $event) {
            $this->jQueryParams[$event] = array();
        }
        return $this;
    }

    /**
     * Zamienia zmienne tablicowe zdarzeń na zmienne łańcuchowe
     * 
     * @return \ZendY\Form\Container\Custom
     */
    protected function _prepareRenderEventParams() {
        foreach ($this->_events as $event) {
            if (count($this->jQueryParams[$event]) > 0) {
                $operations = implode(PHP_EOL, $this->jQueryParams[$event]);
                $this->jQueryParams[$event] = JQuery::createJQueryEventObject(
                                $operations
                );
            } else {
                unset($this->jQueryParams[$event]);
            }
        }
        return $this;
    }

    /**
     * Odświeża dekoratory po zmianach wykonanych na formularzu 
     * po dołaczeniu go do innego kontenera
     * 
     * @return \ZendY\Form\Container\Custom
     */
    public function refreshDecorators() {
        $attribs = \ZendY\View\Helper\Widget::prepareCSS($this->getAttribs());

        $space = $this->_space['value'] . $this->_space['unit'];

        $this->setDecorators(array(
            'FormElements',
            array(array('Inner' => 'HtmlTag'), array(
                    'class' => Css::PADDING_ALL . ' ' . Css::SCROLL_DISABLE
            )),
            array(array('Space' => 'HtmlTag'), array(
                    'class' => $this->_widgetClass,
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
     * Obsługa zdarzenia dołączenia kontenera do formularza nadrzędnego
     * 
     * @return \ZendY\Form\Container\Custom
     */
    public function onContain() {
        return $this;
    }

    /**
     * Ustawia kod operacji dla wystąpienia zdarzenia przewidzianego w kontrolce
     *
     * @param string $event
     * @param string $operation
     * @return \ZendY\Form\Container\Custom
     */
    public function setOnEventParam($event, $operations) {
        $this->setJQueryParam($event, JQuery::createJQueryEventObject($operations));
        return $this;
    }

}