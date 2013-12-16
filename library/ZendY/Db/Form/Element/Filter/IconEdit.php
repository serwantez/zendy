<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element\Filter;

use ZendY\Css;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\Form\Element\Filter\FilterInterface;

/**
 * Filtrujące pole tekstowe z ikoną
 *
 * @author Piotr Zając
 */
class IconEdit extends \ZendY\Db\Form\Element\IconEdit implements FilterInterface {

    /**
     * Operator porównania
     * 
     * @var string
     */
    protected $_operator = DataSet::OPERATOR_BEGIN;

    /**
     * Parametry filtrujące przekazywane do przeglądarki
     * 
     * @var array
     */
    protected $_frontFilterParams = array();

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setIcon(Css::ICON_SEARCH);
    }

    /**
     * Ustawia nazwę pola
     * 
     * @param string $dataField
     * @return \ZendY\Db\Form\Element\Filter\IconEdit
     */
    public function setDataField($dataField) {
        $this->_dataField = $dataField;
        if (isset($this->_dataSource))
            $this->_dataSource->refreshFilterControl($this);
        return $this;
    }

    /**
     * Ustawia operator filtra
     * 
     * @param string $operator
     * @return \ZendY\Db\Form\Element\Filter\IconEdit
     */
    public function setOperator($operator) {
        $this->_operator = $operator;
        return $this;
    }

    /**
     * Zwraca operator filtra
     * 
     * @return string
     */
    public function getOperator() {
        return $this->_operator;
    }

    /**
     * Dodaje parametr filtrujący przekazywany do przeglądarki
     * 
     * @param string $paramName
     * @param string $paramValue
     * @return \ZendY\Db\Form\Element\Filter\IconEdit
     */
    public function setFrontFilterParam($paramName, $paramValue) {
        $this->_frontFilterParams[$paramName] = $paramValue;
        return $this;
    }

    /**
     * Zwraca tablicę parametrów filtrujących przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontFilterParams() {
        $this->setFrontFilterParam('type', 'ed');
        $this->setFrontFilterParam('dataField', $this->getDataField());
        $this->setFrontFilterParam('operator', $this->getOperator());
        return $this->_frontFilterParams;
    }

    /**
     * Renderuje kontrolkę
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasDataSource())
            $this->getDataSource()->addFilterControl($this);
        return parent::render($view);
    }
    
    /**
     * Renderuje kod js odpowiedzialny za dostarczanie danych do kontrolki
     * 
     * @return string
     */
    public function renderDbFilter() {
        $js = sprintf(
                'ds.addFilter("%s",%s);'
                , $this->getId()
                , \ZendY\JQuery::encodeJson($this->getFrontFilterParams())
        );
        return $js;
    }    

}
