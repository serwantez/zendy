<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Msg;
use ZendY\Exception;
use ZendY\Db\Select;

/**
 * Zbiór danych przechowywanych jako wynik zapytania sql
 *
 * @author Piotr Zając
 */
class Query extends Base {

    /**
     * Obiekt zapytania
     * 
     * @var \ZendY\Db\Select
     */
    protected $_select;

    /**
     * Obiekt adaptera bazodanowego
     * 
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Inicjalizuje obiekt
     *
     * @return void
     */
    public function init() {
        parent::init();
        $this->_db = \Zend_Registry::get('db');
        $this->_select = new Select($this->_db);
        $this->_readOnly = true;
    }

    /**
     * Wczytuje wewnętrzne obiekty klasy przy deserializacji zbioru danych
     * 
     * @return void
     */
    public function __wakeup() {
        $this->_db = \Zend_Registry::get('db');
        $this->_select->setAdapter($this->_db);
        parent::__wakeup();
    }

    /**
     * Przechodzi do pierwszego rekordu pasującego do podanych kryteriów
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function searchAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if (isset($params['searchValues'])) {
            $select = clone $this->_select;
            $columnPart = $select->getPart(\ZendY\Db\Select::COLUMNS);
            if (count($this->_filter->getFilters())) {
                $select->where($this->_filter->toSelect($columnPart));
            }
            if (count($this->_order->getSorts())) {
                $select->order($this->_order->toSelect($columnPart));
            }
            $select2 = clone $select;
            $filter = new \ZendY\Db\Filter();
            foreach ($params['searchValues'] as $field => $fieldValue) {
                if (is_array($fieldValue)) {
                    $searched = $fieldValue['value'];
                    $operator = $fieldValue['equalization'];
                } else {
                    $searched = $fieldValue;
                    $operator = self::OPERATOR_EQUAL;
                }
                $filter->addFilter($field, $searched, $operator);
            }
            $select->where($filter->toSelect($columnPart));
            /*if ($this->getId() == 'line_point') {
                print_r($columnPart);
                exit;
            }*/
            try {
                $q = $select->query();
                $array = $q->fetchAll();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                exit($select);
            }
            if (!is_array($array)) {
                $array = $array->toArray();
            }
            $where = '1=1';
            if (count($this->_order->getSorts())) {
                $sorts = $this->_order->getSorts();
                foreach ($sorts as $sort) {
                    //sprawdzenie czy kolumna nie jest aliasem
                    foreach ($columnPart as $columnData) {
                        if ($sort['field'] == $columnData[2]) {
                            if ($columnData[1] instanceof \Zend_Db_Expr) {
                                $sort['field'] = $columnData[1];
                            } else {
                                $sort['field'] = $columnData[0] . '.' . $columnData[1];
                            }
                            break;
                        }
                    }
                    $where .= " and " . $sort['field'] . (strtoupper($sort['direction']) == 'DESC' ? " >= '" : " <= '") . $array[0][$sort['field']] . "'";
                }
            } else {
                $primary = $this->getPrimary();
                foreach ($primary as $field) {
                    //sprawdzenie czy kolumna nie jest aliasem
                    foreach ($columnPart as $columnData) {
                        if ($columnData[2] == $field) {
                            if ($columnData[1] instanceof \Zend_Db_Expr) {
                                $field = $columnData[1];
                            } else {
                                $field = $columnData[0] . '.' . $columnData[1];
                            }
                            break;
                        }
                    }
                    $where .= " and " . $field . " <= '" . $array[0][$field] . "'";
                }
            }
            $select2->reset(\ZendY\Db\Select::COLUMNS);
            $select2->columns("count(*)-1")
                    ->where($where);
            try {
                $q = $select2->query();
                $key = $q->fetchColumn();
                $result = array_merge($result, $this->seekAction(array('offset' => $key), true));
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                exit($select2);
            }
        }
        if (!$compositePart) {
            $this->_setActionState($params);
        }
        return $result;
    }

    /**
     * Zwraca wartości pól klucza głównego w bieżącym rekordzie
     * 
     * @return array 
     */
    public function getPrimaryValue() {
        $values = array();
        if ($this->_state && $this->_offset >= 0) {
            $primary = $this->getPrimary();

            $select = clone $this->_select;

            if (count($this->_filter->getFilters())) {
                $select->where($this->_filter->toSelect($select->getPart(\Zend_Db_Select::COLUMNS)));
            }

            if (count($this->_order->getSorts())) {
                $select->order($this->_order->toSelect());
            }

            $select->limit(1, $this->_offset);

            try {
                $q = $select->query();
            } catch (Exception $exc) {
                echo 'Id: ' . $this->getId() . ' function ' . __FUNCTION__;
                print($select);
                echo $exc->getTraceAsString();
                exit;
            }

            $record = $q->fetchAll();

            if (count($record)) {
                $record = $record[0];
                foreach ($primary as $primaryColumn) {
                    $values[$primaryColumn] = $record[$primaryColumn];
                }
            }
        }
        return $values;
    }

    /**
     * Zwraca rekordy limitowane
     * 
     * @param int|null $offset
     * @param int|null $itemCount
     * @param null|array|string $column
     * @param null|array $conditionalFormats
     * @return array 
     */
    public function getItems($offset = null, $itemCount = null, $columns = '*', $conditionalFormats = null) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $select = clone $this->_select;
        $selectColumns = $select->getPart(Select::COLUMNS);

        //pusta tablica kolumn
        if (is_array($columns) && !count($columns))
            $columns = '*';

        $columnPart = $select->getPart(Select::COLUMNS);

        if (count($this->_filter->getFilters())) {
            $select->where($this->_filter->toSelect($columnPart));
        }

        if (count($this->_order->getSorts())) {
            $select->order($this->_order->toSelect($columnPart));
        }

        $fields = $this->getColumns();
        if ($columns <> '*' && !in_array('*', $fields)) {
            /**
             * @todo zrobić osobną funkcję do szukania kolumn
             */
            $select->reset(Select::COLUMNS);
            $newSelectColumns = array();
            foreach ($columns as $col) {
                $key = array_search($col, $fields);
                if ($key !== false) {
                    $newSelectColumns[] = $selectColumns[$key];
                }
            }
            $select->setPart(Select::COLUMNS, $newSelectColumns);
        }

        if (isset($offset) && isset($itemCount))
            $select->limit($itemCount, $offset);

        if (isset($conditionalFormats) && count($conditionalFormats)) {
            $formatColumn = "(case";
            foreach ($conditionalFormats as $conditionalFormat) {
                $condition = $conditionalFormat[0];
                $format = $conditionalFormat[1];
                $formatColumn .= " when ("
                        . $condition->toSelect($selectColumns)
                        . ") then '"
                        . $format
                        . "'";
            }
            $formatColumn .= " else '' end) as `_format`";
            try {
                $select->columns(new \Zend_Db_Expr($formatColumn));
            } catch (Exception $exc) {
                exit($formatColumn);
            }
        }

        /* if ($this->getId() == 'hafas_location') {
          Msg::add($this->getId() . '-> before query');
          print($select);
          exit();
          } */

        try {
            $q = $select->query();
        } catch (Exception $exc) {
            echo 'Id: ' . $this->getId() . ' function ' . __FUNCTION__;
            print($select);
            echo $exc->getTraceAsString();
            exit;
        }
        $rows = $q->fetchAll();
        if (is_object($rows))
            $rows = $rows->toArray();

        return $rows;
    }

    /**
     * Pobiera wartości z podanej kolumny
     * 
     * @param string $col column name to fetch
     * @return array
     */
    public function fetchCol($col) {
        $select = clone $this->_select;

        $select->reset(Select::COLUMNS);
        $select->columns($col);

        if (count($this->_filter->getFilters())) {
            $select->where($this->_filter->toSelect());
        }

        if (count($this->_order->getSorts())) {
            $select->order($this->_order->toSelect());
        }

        try {
            $q = $select->query();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            echo 'Id: ' . $this->getId() . ' function ' . __FUNCTION__;
            print($select);
            exit;
        }

        $data = $q->fetchAll(\Zend_Db::FETCH_COLUMN, 0);

        return $data;
    }

    /**
     * Zwraca liczbę wszystkich rekordów w zbiorze
     * 
     * @return int
     */
    protected function _count() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $select = clone $this->_select;

        if (count($this->_filter->getFilters())) {
            $select->where($this->_filter->toSelect($select->getPart(\Zend_Db_Select::COLUMNS)));
        }

        //wszystkie kolumny nie są potrzebne
        $select->reset(Select::COLUMNS);
        $select->columns("count(*)");
        //sortowanie nie jest potrzebne
        $select->reset(Select::ORDER);

        try {
            $q = $select->query();
            Msg::add($this->getId() . '->' . __FUNCTION__ . ' after query');
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            echo 'Id: ' . $this->getId() . ' function ' . __FUNCTION__;
            print($select);
            exit;
        }
        return $q->fetchColumn();
    }

    /**
     * Zwraca pola (kolumny) zapytania
     * 
     * @param bool $full czy metoda ma zwracać pełne nazwy kolumn wraz z definicjami aliasów
     * @return array
     */
    public function getColumns($full = false) {
        $fields = array();
        $columns = $this->_select->getPart(\Zend_Db_Select::COLUMNS);
        foreach ($columns as $column) {
            if (isset($column[2])) {
                if ($full)
                    $fields[$column[2]] = $column[0] . '.' . $column[1];
                else
                    $fields[] = $column[2];
            }
            else {
                $fields[] = $column[1];
            }
        }
        return $fields;
    }

    /**
     * Tworzy pusty wiersz
     * 
     * @return array
     */
    public function createRow() {
        $ret = array();
        $fields = $this->getColumns();
        foreach ($fields as $field) {
            $ret[$field] = '';
        }
        return $ret;
    }

    /**
     * Zwraca kolumny zapytania, których nazwa jest aliasem
     * 
     * @return array
     */
    public function getAliasFields() {
        $fields = $this->_select->getPart(Select::COLUMNS);
        return array_filter($fields, function($row) {
                            return isset($row[2]);
                        });
    }

    /**
     * Zwraca opis kolumn tablic zapytania w postaci tablicy asocjacyjnej, 
     * gdzie kluczami są nazwy kolumn lub ich aliasy
     * 
     * @return array
     */
    public function describe() {
        $res = array();
        $froms = $this->_select->getPart(\Zend_Db_Select::FROM);
        $aliasFields = $this->getAliasFields();
        foreach ($froms as $alias => $from) {
            /**
             * @todo obsługa podzapytań
             */
            if (!is_object($from['tableName'])) {
                $cols = $this->_db->describeTable($from['tableName']);
                //obsługa aliasów kolumn
                if (is_array($aliasFields)) {
                    foreach ($aliasFields as $fieldData) {
                        //sprawdzenie czy dana kolumna ma ustawiony alias w zapytaniu
                        if (isset($fieldData[0])
                                && $fieldData[0] == $alias
                                && !is_object($fieldData[1])
                                && array_key_exists($fieldData[1], $cols)) {
                            //zamiana klucza
                            $cols[$fieldData[2]] = $cols[$fieldData[1]];
                            unset($cols[$fieldData[1]]);
                        }
                    }
                }
                $res = array_merge($res, $cols);
            }
        }
        return $res;
    }

    /**
     * Zwraca wszystkie pola typu blob
     * 
     * @return array
     */
    public function getBlobFields() {
        $describe = $this->describe();
        $result = array();
        foreach ($describe as $field => $value) {
            $col = $describe[$field];
            if (in_array($col['DATA_TYPE'], array('blob', 'tinyblob', 'mediumblob', 'longblob')))
                $result[] = $field;
        }
        return $result;
    }

    /**
     * Zwraca bieżący rekord
     * 
     * @param bool $filterBlobs
     * @return array
     */
    public function getCurrent($filterBlobs = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        if ($this->_state && $this->_offset >= 0) {
            $select = clone $this->_select;

            if (count($this->_filter->getFilters())) {
                $select->where($this->_filter->toSelect($select->getPart(\Zend_Db_Select::COLUMNS)));
            }

            if (count($this->_order->getSorts())) {
                $select->order($this->_order->toSelect());
            }

            $select->limit(1, $this->_offset);

            //$mainSelect = $this->_db->select();
            //$mainSelect->from(new Zend_Db_Expr('(' . $select . ')'), '*');

            try {
                $q = $select->query();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                echo 'Id: ' . $this->getId() . ' function ' . __FUNCTION__;
                print($select);
                exit;
            }

            $ret = $q->fetch(\PDO::FETCH_ASSOC);
            if (!$ret) {
                $ret = $this->createRow();
            }
            //return $select->query()->fetch(PDO::FETCH_ASSOC);
        } else {
            $ret = $this->createRow();
        }
        if ($filterBlobs) {
            $blobs = $this->getBlobFields();
            foreach ($blobs as $field) {
                $ret[$field] = 'blob';
            }
        }
        return $ret;
    }

    /**
     * Przekierowanie odwołań do pól i metod na odwołanie do obiektu zapytania
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed the return value of the callback, or <b>FALSE</b> on error.
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->_select, $name), $arguments);
    }

    /**
     * Zwraca treść zapytania SQL
     * 
     * @return string 
     */
    public function getSQL() {
        return $this->_select->__toString();
    }

}
