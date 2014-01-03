<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Form\Element\CellInterface;
use ZendY\Form\Element;

/**
 * Obrazek, którego ścieżka przechowywana jest w zbiorze danych
 *
 * @author Piotr Zając
 */
class ImageView extends Element\Image implements CellInterface {

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
        $this->helper = 'imageView';
    }

    /**
     * Ustawia źródło grafiki
     * 
     * @return \ZendY\Db\Form\Element\ImageView
     */
    protected function _setSource() {
        if (isset($this->_dataSource) && isset($this->_dataField)) {
            $this->setSource($this->getView()->baseUrl() . '/' . \Blueimp\Upload\Handler::$uploadDir . 'noimage.png');
            $this->options['datasource'] = $this->_dataSource->getId();
            $this->options['datafield'] = $this->_dataField;
            $this->setNullPath($this->getView()->baseUrl() . '/' . \Blueimp\Upload\Handler::$uploadDir . 'noimage.png');
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
        $js = sprintf('dc["iv"]["%s"].%s();'
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
     * @return \ZendY\Db\Form\Element\ImageView
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
        $this->setFrontEditParam('type', 'iv');
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
