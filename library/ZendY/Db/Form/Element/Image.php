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
use ZendY\Exception;

/**
 * Obrazek przechowywany w zbiorze danych
 *
 * @author Piotr Zając
 */
class Image extends Element\Image implements CellInterface {

    use CellTrait;
    
    /**
     * Właściwości komponentu
     */

    const PROPERTY_DATASOURCE = 'dataSource';
    const PROPERTY_DATAFIELD = 'dataField';    
    
    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_DATAFIELD,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_ALIGN,
        self::PROPERTY_ALT,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_FIT,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
        self::PROPERTY_NULLPATH,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_UPLOADDIRECTORY,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );    

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'dbImage';
    }
    
    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setFileName($value) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }    

    /**
     * Ustawia źródło grafiki
     * 
     * @return \ZendY\Db\Form\Element\Image
     */
    protected function _setSource() {
        if (isset($this->_dataSource) && isset($this->_dataField)) {
            $this->setAttrib('src', DataSource::$controller .
                    DataSource::$imageAction .
                    '/id/' . $this->_dataSource->getName() .
                    '/field/' . $this->_dataField);
            $this->options['datasource'] = $this->_dataSource->getName();
            $this->options['datafield'] = $this->_dataField;
            $this->setNullPath($this->getFileName() . '/value/empty');
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
                , $this->getName()
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
