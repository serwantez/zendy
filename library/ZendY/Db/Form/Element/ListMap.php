<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Filter;
use ZendY\Db\Form\Element\ColumnInterface;
use ZendY\Exception;

/**
 * Bazowa kontrolka mapy wyświetlającej listę obiektów ze zbioru danych
 *
 * @author Piotr Zając
 */
abstract class ListMap extends \ZendY\Form\Element\ListMap implements ColumnInterface {

    use ColumnTrait;

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Parametry nawigacyjne przekazywane do przeglądarki
     * 
     * @var array
     */
    protected $_frontNaviParams = array();

    /**
     * Tablica warunkowego formatowania wierszy
     * 
     * @var array
     */
    protected $_conditionalRowFormat = array();

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setFrontNaviParam('type', 'mp');
    }

    /**
     * Dodaje parametr przekazywany do przeglądarki
     * 
     * @param string $paramName
     * @param string $paramValue
     * @return \ZendY\Db\Form\Element\ListMap
     */
    public function setFrontNaviParam($paramName, $paramValue) {
        $this->_frontNaviParams[$paramName] = $paramValue;
        return $this;
    }

    /**
     * Zwraca tablicę parametrów nawigacyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontNaviParams() {
        $this->setFrontNaviParam('keyField', $this->getKeyField());
        $this->setFrontNaviParam('listField', $this->getListField());
        $this->setFrontNaviParam('staticRender', $this->getStaticRender());
        return $this->_frontNaviParams;
    }

    /**
     * Zwraca pola ze zbioru danych potrzebne do wyrenderowania kontrolki
     *  
     * @return array
     */
    public function getFields() {
        return array_merge($this->getKeyField(), $this->getListField());
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('dataField', $this->getDataField());
        return parent::getFrontEditParams();
    }

    /**
     * Dodaje warunek formatujący wyświetlane rekordy
     * 
     * @param Filter|array $condition
     * @param string $rowFormat
     * @return \ZendY\Db\Form\Element\ListMap
     */
    public function addConditionalRowFormat($condition, $rowFormat) {
        if ($condition instanceof Filter)
            $condition = $condition->getFilters();
        $this->_conditionalRowFormat[] = array($condition, $rowFormat);
        return $this;
    }

    /**
     * Zwraca warunki formatujące wyświetlane rekordy
     * 
     * @return array
     */
    public function getConditionalRowFormat() {
        return $this->_conditionalRowFormat;
    }

    /**
     * Tworzy tablicę wartości dla statycznego renderowania listy
     * 
     * @return void
     * @throws Exception
     */
    protected function _performList() {
        if (isset($this->_listSource))
            $results = $this->_listSource->getItems();
        else {
            $results = array();
        }

        $options = array();
        if ($this->_emptyValue) {
            $options[''] = '';
        }

        foreach ($results as $r) {
            //utworzenie wartości dla kontrolki
            $keyValueArray = array();
            if (is_object($r))
                $r = $r->toArray();

            foreach ($this->_keyField as $keyField) {
                if (!array_key_exists($keyField, $r)) {
                    throw new Exception(
                            'Kolumna klucza ' . $keyField . ' nie jest obecna w wyniku zapytania');
                }
                $keyValueArray[] = $r[$keyField];
            }

            $keyValueString = implode(';', $keyValueArray);

            $option = '';
            $c = 0;
            foreach ($this->_listField as $field) {
                if (!array_key_exists($field, $r)) {
                    throw new Exception(
                            'Kolumna wyświetlana ' . $field . ' nie jest obecna w wyniku zapytania');
                }
                if ($c > 0)
                    $option .= $this->_columnSpace;
                $option .= $r[$field];
                $c++;
            }
            $options[$keyValueString] = $option;
        }
        $this->setMultiOptions($options);
    }

    /**
     * Renderuje kontrolkę
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasDataSource())
            $this->getDataSource()->addEditControl($this);
        //renderowanie statyczne
        if ($this->_staticRender) {
            $this->setDisabled(FALSE);
            $this->_performList();
        }
        return parent::render($view);
    }

}
