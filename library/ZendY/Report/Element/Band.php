<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Element;

use ZendY\Exception;
use ZendY\Report\Element\Band\Column;
use ZendY\Db\DataInterface;

/**
 * Wstęga danych w raporcie
 * 
 * @return void
 */
class Band extends Multi implements DataInterface {

    use \ZendY\Db\DataTrait;

    /**
     * Domyślny pomocnik widoku
     * 
     * @var string
     */
    public $helper = 'reportBand';

    /**
     * Dodaje kolumnę
     * 
     * @param \ZendY\Report\Element\Band\Column $column
     * @return \ZendY\Report\Element\Band
     */
    public function addColumn(Column $column) {
        $columns = $this->getColumns();
        $columns[$column->getName()] = $column;
        $this->setAttrib('columns', $columns);
        return $this;
    }

    /**
     * Dodaje wiele kolumn na raz
     * 
     * @param array $columns
     * @return \ZendY\Report\Element\Band
     */
    public function addColumns(array $columns) {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
        return $this;
    }

    /**
     * Zwraca wszystkie kolumny
     * 
     * @return array 
     */
    public function getColumns() {
        return $this->getAttrib('columns');
    }

    /**
     * Zwraca kolumnę o podanym identyfikatorze
     * 
     * @param string $columnId
     * @return \ZendY\Report\Element\Band\Column
     */
    public function getColumn($columnId) {
        $columns = $this->getAttrib('columns');
        return $columns[$columnId];
    }

    /**
     * Zwraca właściwości kolumn
     * 
     * @return array
     */
    public function getColumnsOptions() {
        $options = array();
        foreach ($this->getColumns() as $column) {
            $options[$column->getName()] = $column->getAttribs();
        }
        return $options;
    }

    /**
     * Tworzy listę wartości
     * 
     * @return void
     * @throws Exception
     */
    protected function _performList() {
        if ($this->hasDataSource()) {
            $results = $this->_dataSource->getDataSet()->getItems();
        } else {
            $results = array();
        }
        $this->setMultiOptions($results);
    }

}
