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
 * Kontrolka bazodanowa prezentująca dane w postaci siatki
 *
 * @author Piotr Zając
 */
class Grid extends \ZendY\Form\Element\Grid implements ColumnInterface {

    use ColumnTrait;

    /**
     * Właściwości komponentu
     */

    const PROPERTY_DATASOURCE = 'dataSource';
    const PROPERTY_DATAFIELD = 'dataField';
    const PROPERTY_LISTSOURCE = 'listSource';
    const PROPERTY_LISTFIELD = 'listField';
    const PROPERTY_KEYFIELD = 'keyField';
    const PROPERTY_STATICRENDER = 'staticRender';

    /**
     * Tablica właściwości
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_DATAFIELD,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_KEYFIELD,
        self::PROPERTY_LISTFIELD,
        self::PROPERTY_LISTSOURCE,
        self::PROPERTY_STATICRENDER,
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_COLUMNS,
        self::PROPERTY_COLUMNSPACE,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_DISABLED,
        self::PROPERTY_EMPTYVALUE,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
        self::PROPERTY_PAGER,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_SORTER,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
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
            $results = $this->_listSource->getDataSet()->getItems();
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

            foreach ($this->_listField as $field) {
                if (!array_key_exists($field, $r)) {
                    throw new Exception(
                            'Kolumna wyświetlana ' . $field . ' nie jest obecna w wyniku zapytania');
                }
                $option[$field] = $r[$field];
            }
            $options[$keyValueString] = $option;
        }
        $this->clearMultiOptions();
        $this->addMultiOptions($options);
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
                $data[$key][$column->getName()] = $column->cellValue($row);
            }
        }
        return $data;
    }

}