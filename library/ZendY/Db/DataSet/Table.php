<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Msg;
use ZendY\Exception;

/**
 * Zbiór danych przechowywanych w tabeli
 *
 * @author Piotr Zając
 */
class Table extends Editable implements TableInterface {

    use TableTrait;

    /**
     * Obiekt adaptera bazodanowego
     * 
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Ustawia wartości domyślne
     *
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        //utworzenie obiektu tabeli
        $this->_table = new \Zend_Db_Table();
        $this->_db = $this->_table->getAdapter();

        //domyślnie tabela nie jest tylko do odczytu
        $this->_readOnly = FALSE;
    }

    /**
     * Wczytuje wewnętrzne obiekty klasy przy deserializacji zbioru danych
     * 
     * @return void
     */
    public function __wakeup() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $this->_db = \Zend_Registry::get('db');
        $this->_table->setOptions(array(\Zend_Db_Table_Abstract::ADAPTER => $this->_db));
        parent::__wakeup();
    }

    /**
     * Ustawia nazwę tabeli
     * 
     * @param string $tableName
     * @return \ZendY\Db\DataSet\Table
     */
    public function setTableName($tableName) {
        $this->_name = $tableName;
        $this->_table->setOptions(array(\Zend_Db_Table_Abstract::NAME => $tableName));
        return $this;
    }

    /**
     * Zwraca nazwę tabeli
     * 
     * @return string
     */
    public function getTableName() {
        if (isset($this->_name))
            return $this->_name;
        else
            return $this->_table->info(\Zend_Db_Table_Abstract::NAME);
    }

    /**
     * Ustawia klucz główny
     * 
     * @param string|array $primary
     * @return \ZendY\Db\DataSet\Table
     */
    public function setPrimary($primary) {
        if (!is_array($primary))
            $primary = array($primary);
        $this->_primary = $primary;
        $this->_table->setOptions(array(\Zend_Db_Table_Abstract::PRIMARY => $primary));
        return $this;
    }

    /**
     * Zwraca pola klucza głównego
     * 
     * @return array
     */
    public function getPrimary() {
        if (!isset($this->_primary))
            $this->_primary = $this->_table->info(\Zend_Db_Table_Abstract::PRIMARY);
        return $this->_primary;
    }

    /**
     * Zwraca opis kolumn tabeli w postaci tablicy asocjacyjnej, 
     * gdzie kluczami są nazwy kolumn
     * 
     * @return array
     */
    public function describe() {
        return $this->_db->describeTable($this->getTableName());
    }

    /**
     * Pobiera rekordy limitowane
     * 
     * @param int|null $offset
     * @param int|null $itemCount
     * @param null|array|string $columns
     * @param null|array $conditionalFormats
     * @return array
     */
    public function getItems($offset = null, $itemCount = null, $columns = '*', $conditionalFormats = null) {
        Msg::add(sprintf('Table %s ->%s', $this->getId(), __FUNCTION__));
        if (!count($columns))
            $columns = '*';
        $select = $this->_db->select();
        $tableName = $this->getTableName();
        \ZendY\Msg::add(sprintf('After tablename %s', __FUNCTION__));
        $select->from($tableName, $columns);

        if (count($this->_filter->getFilters())) {
            $select->where($this->_filter->toSelect());
        }

        if (count($this->_order->getSorts())) {
            $select->order($this->_order->toSelect());
        }

        if (isset($offset) && isset($itemCount))
            $select->limit($itemCount, $offset);

        if (isset($conditionalFormats) && count($conditionalFormats)) {
            $formatColumn = "(case";
            foreach ($conditionalFormats as $conditionalFormat) {
                $condition = $conditionalFormat[0];
                $format = $conditionalFormat[1];
                $formatColumn .= " when (" . $condition->toSelect() . ") then '" . $format . "'";
            }
            $formatColumn .= " else '' end) as `_format`";
            $select->columns(new Zend_Db_Expr($formatColumn));
        }

        try {
            $q = $select->query();
            $rows = $q->fetchAll();
            if (is_object($rows))
                $rows = $rows->toArray();
        } catch (Exception $exc) {
            echo $exc->getMessage() . '<br />';
            echo $exc->getTraceAsString();
            $rows = array();
        }

        return $rows;
    }

    /**
     * Zwraca wszystkie pola (kolumny) tabeli
     * 
     * @return array
     */
    public function getColumns() {
        $fields = $this->_table->info(\Zend_Db_Table_Abstract::COLS);
        return $fields;
    }

    /**
     * Zwraca nazwę kolumny tabeli na podstawie jej aliasu
     * 
     * @param string $alias
     * @return string
     */
    public function getTableField($alias) {
        return $alias;
    }

    /**
     * Pobiera wartości z podanej kolumny
     * 
     * @param string $col column name to fetch
     * @param string|null $where SQL composing optional WHERE clause
     * @param string|null $order SQL composing optional ORDER BY clause
     * @return array
     */
    public function fetchCol($col, $where = null, $order = null) {
        $select = $this->_table->select();
        $select->from($this->_name, $col);

        if (count($this->_filter->getFilters())) {
            $select->where($this->_filter->toSelect());
        }

        if (count($this->_order->getSorts())) {
            $select->order($this->_order->toSelect());
        }

        if (!is_null($where)) {
            $select->where($where);
        }

        if (!is_null($order)) {
            $select->order($order);
        }

        return $this->_db->fetchCol($select);
    }

    /**
     * Pobiera pierwszy wiersz podanej kolumny
     * 
     * @param string $col
     * @param string|null $where
     * @param string|null $order
     * @return string
     */
    public function fetchOne($col, $where = null, $order = null) {
        $select = $this->_table->select();
        $select->from($this->_name, $col);

        if (count($this->_filter->getFilters())) {
            $select->where($this->_filter->toSelect());
        }

        if (count($this->_order->getSorts())) {
            $select->order($this->_order->toSelect());
        }

        if (!is_null($where)) {
            $select->where($where);
        }

        if (!is_null($order)) {
            $select->order($order);
        }

        //tylko bieżący wiersz
        $select->limit(1, $this->_offset);

        return $this->_db->fetchOne($select);
    }

    /**
     * Zwraca liczbę wszystkich rekordów w tabeli z uwzględnieniem filtra
     * 
     * @return int
     */
    protected function _count() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $select = $this->_db->select();
        $select->from($this->_name, 'COUNT(*) AS num');

        if (count($this->_filter->getFilters())) {
            $select->where($this->_filter->toSelect());
        }

        if (count($this->_order->getSorts())) {
            $select->order($this->_order->toSelect());
        }
        $row = $this->_db->fetchOne($select);

        return $row;
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
            $select = $this->_table->select(true);

            if (count($this->_filter->getFilters())) {
                $select->where($this->_filter->toSelect());
            }

            if (count($this->_order->getSorts())) {
                $select->order($this->_order->toSelect());
            }

            $select->limit(1, $this->_offset);
            $row = $this->_table->fetchRow($select);
        } else {
            $row = $this->_table->createRow();
        }
        $row = $row->toArray();
        if ($filterBlobs) {
            $blobs = $this->getBlobFields();
            foreach ($blobs as $field) {
                $row[$field] = 'blob';
            }
        }
        return $row;
    }

    /**
     * Zwraca wartości pól klucza głównego w bieżącym rekordzie
     * 
     * @return array|null
     */
    public function getPrimaryValue() {
        if ($this->_state && $this->_offset >= 0) {
            $primary = $this->getPrimary();

            $select = $this->_table->select(true);

            if (count($this->_filter->getFilters())) {
                $select->where($this->_filter->toSelect());
            }

            if (count($this->_order->getSorts())) {
                $select->order($this->_order->toSelect());
            }

            $select->limit(1, $this->_offset);

            $record = $this->_table->fetchRow($select);

            $values = array();

            if (isset($record)) {
                $record = $record->toArray();
                foreach ($primary as $primaryColumn) {
                    $values[$primaryColumn] = $record[$primaryColumn];
                }
                return $values;
            }
        }
        return null;
    }

    /**
     * Czy dane pole jest typu blob
     * 
     * @param string $field
     * @return bool
     */
    public function isBlob($field) {
        //dane tabeli
        $describe = $this->describe();
        $col = $describe[$field];
        if (in_array($col['DATA_TYPE'], array('blob', 'tinyblob', 'mediumblob', 'longblob'))) {
            return TRUE;
        } else
            return FALSE;
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
     * Usuwa klucz główny
     * 
     * @return \ZendY\Db\DataSet\Table
     */
    public function dropPrimaryKey() {
        $q = "alter table `" . $this->_name . "` drop primary key";
        $this->_db->query($q);
        return $this;
    }

    /**
     * Dodaje klucz główny
     * 
     * @param string $field
     * @return \ZendY\Db\DataSet\Table
     */
    public function addPrimaryKey($field) {
        $q = "ALTER TABLE `" . $this->_name . "` ADD PRIMARY KEY (`" . $field . "`)";
        $this->_db->query($q);
        return $this;
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
            $select1 = $this->_table->select(true);
            if (count($this->_filter->getFilters())) {
                $select1->where($this->_filter->toSelect());
            }
            if (count($this->_order->getSorts())) {
                $select1->order($this->_order->toSelect());
            }
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
            $select2 = $this->_db->select()
                    ->from(array('s1' => new \Zend_Db_Expr('(' . $select1 . ')')), array(
                '*',
                'row' => new \Zend_Db_Expr('@row:=@row+1')
                    ));
            $select3 = $this->_db->select()
                    ->from(array('s2' => new \Zend_Db_Expr('(' . $select2 . ')')), 'row')
                    ->where($filter->toSelect())
            ;

            try {
                $this->_db->query("set @row:=-1");
                $q = $select3->query();
                $offset = $q->fetchColumn();
                $result = array_merge($result, $this->seekAction(array('offset' => $offset), true));
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        if (!$compositePart) {
            $this->_setActionState($params);
        }
        return $result;
    }

}
