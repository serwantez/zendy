<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataSource;
use ZendY\Db\Form\Element\CellInterface;
use ZendY\Form\Element;

/**
 * Obrazek przechowywany w zbiorze danych
 *
 * @author Piotr Zając
 */
class Image extends Element\Image implements CellInterface {

    use CellTrait;

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->helper = 'dbImage';
    }

    /**
     * Ustawia źródło grafiki
     * 
     * @return \ZendY\Db\Form\Element\Image
     */
    protected function _setSource() {
        if (isset($this->_dataSource) && isset($this->_dataField)) {
            $this->setSource(DataSource::$controller .
                    DataSource::$imageAction .
                    '/id/' . $this->_dataSource->getId() .
                    '/field/' . $this->_dataField);
            $this->options['datasource'] = $this->_dataSource->getId();
            $this->options['datafield'] = $this->_dataField;
            $this->setNullPath($this->getSource() . '/value/empty');
        }
        return $this;
    }

    /**
     * Zwraca kod js wykonania podanej metody
     * 
     * @param string $method
     * @return string
     */
    public function getJQueryMethod($method) {
        $js = sprintf('dc["im"]["%s"].%s();'
                , $this->getId()
                , $method
        );
        return $js;
    }

    /**
     * Dodaje do wskazanego elementu kod otwarcia systemowego okna przesyłania pliku 
     * przy podanym zdarzeniu
     * 
     * @param \ZendY\Form\Element\Widget|null $element
     * @param string $event
     * @return \ZendY\Form\Container\Dialog
     */
    public function addLoader($element, $event = \ZendY\JQuery::EVENT_CLICK) {
        if ($element instanceof Element\Widget) {
            $element->setOnEvent($event, $this->getJQueryMethod(self::PARAM_METHOD_LOAD));
        }
        return $this;
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('type', 'im');
        $this->setFrontEditParam('dataField', $this->getDataField());
        return parent::getFrontEditParams();
    }

    /**
     * Renderuje kontrolkę
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasDataSource()) {
            $this->getDataSource()->addEditControl($this);
            $this->_setSource();
        }
        return parent::render($view);
    }

}
