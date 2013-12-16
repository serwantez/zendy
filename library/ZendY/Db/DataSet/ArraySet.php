<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Msg;

/**
 * Zbiór przechowujący dane w postaci tablicy
 *
 * @author Piotr Zając
 */
class ArraySet extends Base {

    /**
     * Dane tablicowe w formacie macierzy MxN:
     * array(
     * 0 => array('pole0' => 'wartosc0_0', 'pole1' => 'wartosc0_1',... 'poleM' => 'wartosc0_M'),
     * 1 => array('pole0' => 'wartosc1_0', 'pole1' => 'wartosc1_1',... 'poleM' => 'wartosc1_M'),
     * ...
     * N => array('pole0' => 'wartoscN_0', 'pole1' => 'wartoscN_1',... 'poleM' => 'wartoscN_M')
     * )
     * 
     * @var array
     */
    protected $_data = array();

    /**
     * Inicjalizuje obiekt
     *
     * @return void
     */
    public function init() {
        parent::init();
        $this->_readOnly = true;
    }

    /**
     * Ustawia dane tablicowe
     * 
     * @param array $data
     * @return \ZendY\Db\DataSet\ArraySet
     */
    public function setData(array $data) {
        $this->_data = $data;
        return $this;
    }

    /**
     * Zwraca dane tablicowe
     * 
     * @return array
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * Tworzy pusty wiersz
     * 
     * @return array
     */
    protected function _createRow() {
        $row = array();
        foreach ($this->_data[0] as $fieldName => $value) {
            $row[$fieldName] = '';
        }
        return $row;
    }

    /**
     * Sortuje tablicę według podanych kryteriów sortowania
     * 
     * @param array $array
     * @param array|null $sort
     */
    protected function _sort(&$array, $sort = null) {
        if (!isset($sort))
            $sort = $this->_order->getSorts();
        $function = '';
        while (list($key) = each($sort)) {
            if (isset($sort[$key]['case']) && ($sort[$key]['case'] == TRUE)) {
                $function .= 'if (strtolower($a["' . $sort[$key]['field'] . '"])<>strtolower($b["' . $sort[$key]['field'] . '"])) { return (strtolower($a["' . $sort[$key]['field'] . '"]) ';
            } else {
                $function .= 'if ($a["' . $sort[$key]['field'] . '"]<>$b["' . $sort[$key]['field'] . '"]) { return ($a["' . $sort[$key]['field'] . '"] ';
            }
            if (isset($sort[$key]['direction']) && ($sort[$key]['direction'] == "DESC")) {
                $function .= '<';
            } else {
                $function .= '>';
            }
            if (isset($sort[$key]['case']) && ($sort[$key]['case'] == TRUE)) {
                $function .= ' strtolower($b["' . $sort[$key]['field'] . '"])) ? 1 : -1; } else';
            } else {
                $function .= ' $b["' . $sort[$key]['field'] . '"]) ? 1 : -1; } else';
            }
        }
        $function .= ' { return 0; }';
        usort($array, create_function('$a, $b', $function));
    }

    /**
     * Filtruje tablicę według podanych kryteriów
     * 
     * @param array $array
     * @param null|callable $filter
     * @return array
     */
    protected function _filter(array $array, $filter = null) {
        $array = array_filter($array, function($el) {
                    if (!isset($filter))
                        return $this->_filter->getCondition($el); else
                        return $filter($el);
                });
        //reindeksacja
        return array_values($array);
    }

    /**
     * Zwraca wybrane kolumny danych
     * uwaga: od wersji php 5.5 istnieje mozliwość użycia funkcji array_column
     * 
     * @param array $data
     * @param array $columns
     * @return array
     */
    protected function _getFieldsData(array $data, array $columns) {
        $ret = array();
        foreach ($data as $key => $row) {
            foreach ($columns as $column) {
                if (isset($column))
                    $ret[$key][$column] = $row[$column];
            }
        }
        return $ret;
    }

    /**
     * Zwraca bieżący rekord
     * 
     * @return array
     */
    public function getCurrent() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $data = $this->getData();

        if (count($this->_filter->getFilters())) {
            $data = $this->_filter($data);
        }

        if (count($this->_order->getSorts())) {
            $this->_sort($data);
        }

        if (array_key_exists($this->_offset, $data))
            return $data[$this->_offset];
        else
            return $this->_createRow();
    }

    /**
     * Zwraca dane limitowane
     * 
     * @param int $offset
     * @param int $itemCount
     * @param null|array|string $column
     * @param null|array $conditionalFormats
     * @return array
     */
    public function getItems($offset = null, $itemCount = null, $columns = null, $conditionalFormats = null) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $data = $this->getData();

        if (count($this->_filter->getFilters())) {
            $data = $this->_filter($data);
        }

        if (count($this->_order->getSorts())) {
            $this->_sort($data, $this->_order->getSorts());
        }

        if (isset($columns) && count($columns)) {
            $data = $this->_getFieldsData($data, $columns);
        }

        if (isset($offset) && isset($itemCount)) {
            $data = array_slice($data, $offset, $itemCount);
        }
        return $data;
    }

    /**
     * Zwraca liczbę wszystkich rekordów w zbiorze z uwzględnieniem filtra
     * 
     * @return int
     */
    protected function _count() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $data = $this->getData();

        if (count($this->_filter->getFilters())) {
            $data = $this->_filter($data);
        }

        return count($data);
    }

    /**
     * Zwraca pola (kolumny) zbioru
     * 
     * @return array
     */
    public function getFields() {
        $fields = array();
        if (isset($this->_data) && count($this->_data)) {
            $fields = array_keys($this->_data[0]);
        }
        return $fields;
    }

    /**
     * Ustawia kolumny, kierunek sortowania oraz wrażliwość na duże/małe litery
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function sortAction($params = array(), $compositePart = false) {
        $result = array();
        if (isset($params['field'])) {
            if (!isset($params['direction']))
                $params['direction'] = 'asc';
            if (!isset($params['case']))
                $params['case'] = TRUE;
            if ($params['direction'] == 'clear') {
                $this->_order->removeSort($params['field']);
            } else {
                $this->_order->setSort(array('field' => $params['field'], 'direction' => $params['direction'], 'case' => $params['case']));
            }
            if (!$compositePart) {
                $this->_setActionState();
            }
        }
        return $result;
    }

}