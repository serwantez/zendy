<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Form\Element\ColumnInterface;
use ZendY\Exception;
use ZendY\Form\Element\Grid\Column;

/**
 * Kontrolka bazodanowa prezentująca dane w postaci struktury drzewiastej
 *
 * @author Piotr Zając
 */
class Grid extends \ZendY\Form\Element\Grid implements ColumnInterface {

    use ColumnTrait;

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Czy ma być wywoływane zdarzenie po kliknieciu
     * 
     * @var bool 
     */
    protected $_changeClick = true;

    /**
     * Dodaje kolumnę
     * 
     * @param \ZendY\Form\Element\Grid\Column $column
     * @return \ZendY\Db\Form\Element\Grid
     */
    public function addColumn(Column $column) {
        parent::addColumn($column);
        $this->_listField[] = $column->getName();
        return $this;
    }

    /**
     * Ustawia czy lista po kliknieciu ma wywołać zdarzenie search
     * 
     * @param bool $changeClick
     * @return \ZendY\Db\Form\Element\Grid
     */
    public function setChangeClick($changeClick) {
        $this->_changeClick = $changeClick;
        return $this;
    }

    /**
     * Zwraca informację o tym, czy lista po kliknieciu 
     * ma wywołać zdarzenie search
     * 
     * @return bool
     */
    public function getChangeClick() {
        return $this->_changeClick;
    }

    /**
     * Ustawia liczbę rekordów na stronę
     * 
     * @param int $rpp
     * @return \ZendY\Db\Form\Element\Grid
     */
    public function setRecordPerPage($rpp) {
        if ($this->hasListSource())
            $this->_listSource->getDataSet()->setRecordPerPage($rpp);
        return $this;
    }

    /**
     * Zwraca liczbę rekordów na stronę
     * 
     * @return int
     */
    public function getRecordPerPage() {
        if ($this->hasListSource())
            return $this->_listSource->getDataSet()->getRecordPerPage();
        else
            return self::DEFAULT_RECORDPERPAGE;
    }

    /**
     * Zwraca tablicę parametrów nawigacyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontNaviParams() {
        $this->setFrontNaviParam('type', 'gr');
        $this->setFrontNaviParam('keyField', $this->getKeyField());
        $this->setFrontNaviParam('listField', $this->getListField());
        $this->setFrontNaviParam('columnsOptions', $this->getColumnsOptions());
        $this->setFrontNaviParam('staticRender', $this->getStaticRender());
        return parent::getFrontNaviParams();
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('type', 'gr');
        $this->setFrontEditParam('dataField', $this->getDataField());
        return parent::getFrontEditParams();
    }

    /**
     * Zwraca pola ze zbioru danych potrzebne do wyrenderowania kontrolki
     *  
     * @return array
     */
    public function getFields() {
        return array_unique(array_merge(
                                $this->getKeyField()
                                , $this->getListField()
                        ));
    }

    /**
     * Tworzy tablicę wartości dla statycznego renderowania listy
     * 
     * @return void
     * @throws Exception
     */
    protected function _performList() {
        if ($this->hasListSource())
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

    /**
     * Formatuje kolumny danych według zdefiniowanych dekoratorów
     * 
     * @param array $data
     * @return array
     */
    public function formatData(array $data = array()) {
        $columns = $this->getColumns();
        foreach ($data as $key => $row) {
            foreach ($columns as $column) {
                $data[$key][$column->getId()] = $column->cellValue($row);
            }
        }
        return $data;
    }

}