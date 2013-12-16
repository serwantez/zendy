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

/**
 * Klasa bazowa dla kontenerów formularza
 *
 * @author Piotr Zając
 */
abstract class Base extends Form {

    use \ZendY\JQueryTrait;

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
     * Konstruktor
     *
     * @param string|null $id
     * @param array|Zend_Config|null $options
     * @return void
     */
    public function __construct($id = null, $options = null) {
        if (isset($id)) {
            $this->setName($id);
        } else {
            $this::$count++;
            $this->setName(get_class($this) . '_' . $this::$count);
        }
        parent::__construct($options);
    }

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->removeDecorator('Form');
        $this->setIsSubForm(true);
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

        $this->setDecorators(array(
            'FormElements',
            array(array('Inner' => 'HtmlTag'), array(
                    'class' => Css::PADDING_ALL . ' ' . Css::SCROLL_DISABLE
            )),
            array(array('Outer' => 'HtmlTag'), $attribs)
        ));
        return $this;
    }

    /**
     * Obsługa zdarzenia dołączenia nawigatora do formularza nadrzędnego
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