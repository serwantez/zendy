<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

use ZendY\Object;
use ZendY\Db\DataSet\Base as DataSet;

/**
 * Filtr zbioru
 *
 * @author Piotr Zając
 */
class Filter extends Object {

    /**
     * Warunki filtrujące
     * 
     * @var array
     */
    protected $_filters = array();

    /**
     * Konstruktor
     * 
     * @param array $filters
     * @return void
     */
    public function __construct(array $filters = array()) {
        parent::__construct();

        if (isset($filters)) {
            $this->setFilters($filters);
        }
    }

    /**
     * Zamienia obiekt w łańcuch zapytania sql
     * 
     * @return string
     */
    public function __toString() {
        return $this->toSelect();
    }

    /**
     * Ustawia warunki filtrujące
     * 
     * @param array $filters
     * @return \ZendY\Db\Filter
     */
    public function setFilters(array $filters) {
        foreach ($filters as $key => $filter) {
            $this->setFilter($key, $filter);
        }
        return $this;
    }

    /**
     * Dodaje warunek filtrujący
     * 
     * @param string $filterName
     * @param array $filter
     * @return \ZendY\Db\Filter
     */
    public function setFilter($filterName, array $filter) {
        $defaultConnector = DataSet::CONNECTOR_AND;
        $defaultOperator = DataSet::OPERATOR_EQUAL;
        foreach ($filter as $field => $data) {
            if (is_array($data)) {
                if (!array_key_exists('operator', $data))
                    $data['operator'] = $defaultOperator;
                if (!array_key_exists('connector', $data))
                    $data['connector'] = $defaultConnector;
            } else {
                $data = array(
                    'value' => $data,
                    'operator' => $defaultOperator,
                    'connector' => $defaultConnector
                );
            }
            $filter[$field] = $data;
        }
        $this->_filters[$filterName] = $filter;
        return $this;
    }

    /**
     * Dodaje filtr na pojedynczym polu
     * 
     * @param string $field
     * @param mixed|null $value
     * @param string|null $operator
     * @return \ZendY\Db\Filter
     */
    public function addFilter($field, $value = 0, $operator = DataSet::OPERATOR_EQUAL) {
        $filter[$field] = array(
            'value' => $value,
            'operator' => $operator,
            'connector' => DataSet::CONNECTOR_AND
        );
        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Zwraca warunki fitrujące
     * 
     * @return array
     */
    public function getFilters() {
        return $this->_filters;
    }

    /**
     * Zwraca filtr o podanej nazwie
     * 
     * @param string $filterName
     * @return array
     */
    public function getFilter($filterName) {
        return $this->_filters[$filterName];
    }

    /**
     * Usuwa filtr o podanej nazwie
     * 
     * @param string $filterName
     * @param string|null $subFilterName
     * @return \ZendY\Db\Filter
     */
    public function clearFilter($filterName, $subFilterName = null) {
        if (isset($subFilterName) && array_key_exists($subFilterName, $this->_filters[$filterName]))
            unset($this->_filters[$filterName][$subFilterName]);
        else
            unset($this->_filters[$filterName]);
        return $this;
    }

    /**
     * Usuwa wszystkie filtry
     * 
     * @return \ZendY\Db\Filter
     */
    public function clearFilters() {
        $this->_filters = array();
        return $this;
    }

    /**
     * Zwraca pierwszą znalezioną wartość filtra dla podanego pola
     * 
     * @param string $field
     * @return mixed|null
     */
    public function getFieldValue($field) {
        foreach ($this->_filters as $filter) {
            foreach ($filter as $f => $data) {
                if ($f == $field) {
                    return $data['value'];
                }
            }
        }
        return null;
    }

    /**
     * Zwraca filtr w postaci części "where" zapytania sql
     * 
     * @param array $columns kolumny z obiektu zapytania sql
     * 
     * @return string 
     */
    public function toSelect(array $columns = array(), $db = null) {
        $res = '1 = 1';
        $cond = isset($db) && ($db instanceof \Zend_Db_Adapter_Abstract);
        if (!$cond) {
            if (\Zend_Registry::isRegistered('db')) {
                $db = \Zend_Registry::get('db');
                $cond = isset($db) && ($db instanceof \Zend_Db_Adapter_Abstract);
            }
        }
        foreach ($this->_filters as $filter) {
            foreach ($filter as $field => $data) {
                if ($data['value'] !== '') {
                    $searched = $data['value'];
                    $operator = $data['operator'];
                    $connector = $data['connector'];

                    if ($operator == DataSet::OPERATOR_BEGIN
                            || $operator == DataSet::OPERATOR_NOT_BEGIN
                            || $operator == DataSet::OPERATOR_CONTAIN
                            || $operator == DataSet::OPERATOR_NOT_CONTAIN) {
                        $searched .= '%';
                    }

                    if ($operator == DataSet::OPERATOR_END
                            || $operator == DataSet::OPERATOR_NOT_END
                            || $operator == DataSet::OPERATOR_CONTAIN
                            || $operator == DataSet::OPERATOR_NOT_CONTAIN) {
                        $searched = '%' . $searched;
                    }

                    if ($cond && !($searched instanceof \Zend_Db_Expr)) {
                        $searched = $db->quote($searched);
                    }

                    if (is_array($searched)) {
                        $searched = implode(',', $searched);
                    }

                    //wyłuskanie domeny
                    $dot = strpos($field, '.');
                    if ($dot !== false) {
                        $domain = '`' . substr($field, 0, $dot) . '`.';
                        $field = substr($field, $dot + 1);
                        $uniqueField = $domain . '`' . $field . '`';
                    } else {
                        $uniqueField = '`' . $field . '`';
                    }

                    //sprawdzenie czy kolumna jest aliasem lub występuje z domeną
                    foreach ($columns as $columnData) {
                        if ($columnData[2] == $field
                                || ($field == $columnData[1] && is_null($columnData[2]))) {
                            if ($columnData[1] instanceof \Zend_Db_Expr) {
                                $uniqueField = $columnData[1];
                            } else {
                                $uniqueField = '`' . $columnData[0] . '`.`' . $columnData[1] . '`';
                            }
                            break;
                        }
                    }

                    if ($operator == DataSet::OPERATOR_EQUAL)
                        $res .= " " . $connector . " " . $uniqueField . " = " . $searched;
                    elseif ($operator == DataSet::OPERATOR_NOT_EQUAL)
                        $res .= " " . $connector . " " . $uniqueField . " <> " . $searched;
                    elseif ($operator == DataSet::OPERATOR_GREATER)
                        $res .= " " . $connector . " " . $uniqueField . " > " . $searched;
                    elseif ($operator == DataSet::OPERATOR_GREATER_EQUAL)
                        $res .= " " . $connector . " " . $uniqueField . " >= " . $searched;
                    elseif ($operator == DataSet::OPERATOR_LESS)
                        $res .= " " . $connector . " " . $uniqueField . " < " . $searched;
                    elseif ($operator == DataSet::OPERATOR_LESS_EQUAL)
                        $res .= " " . $connector . " " . $uniqueField . " <= " . $searched;
                    elseif ($operator == DataSet::OPERATOR_BEGIN)
                        $res .= " " . $connector . " " . $uniqueField . " like " . $searched;
                    elseif ($operator == DataSet::OPERATOR_NOT_BEGIN)
                        $res .= " " . $connector . " " . $uniqueField . " not like " . $searched;
                    elseif ($operator == DataSet::OPERATOR_END)
                        $res .= " " . $connector . " " . $uniqueField . " like " . $searched;
                    elseif ($operator == DataSet::OPERATOR_NOT_END)
                        $res .= " " . $connector . " " . $uniqueField . " not like " . $searched;
                    elseif ($operator == DataSet::OPERATOR_CONTAIN)
                        $res .= " " . $connector . " " . $uniqueField . " like " . $searched;
                    elseif ($operator == DataSet::OPERATOR_NOT_CONTAIN)
                        $res .= " " . $connector . " " . $uniqueField . " not like " . $searched;
                    elseif ($operator == DataSet::OPERATOR_IN)
                        $res .= " " . $connector . " " . $uniqueField . " in (" . $searched . ")";
                    elseif ($operator == DataSet::OPERATOR_NOT_IN)
                        $res .= " " . $connector . " " . $uniqueField . " not in (" . $searched . ")";
                    elseif ($operator == DataSet::OPERATOR_IS_NULL)
                        $res .= " " . $connector . " " . $uniqueField . " is null";
                    elseif ($operator == DataSet::OPERATOR_IS_NOT_NULL)
                        $res .= " " . $connector . " " . $uniqueField . " is not null";
                }
            }
        }

        return $res;
    }

    /**
     * Zwraca wartość logiczną warunku filtrowania dla podanego wiersza
     * 
     * @param array $row
     * @return bool
     */
    public function getCondition(array $row) {
        $res = true;
        foreach ($this->_filters as $filter) {
            foreach ($filter as $field => $value) {
                $searched = $value['value'];
                $operator = $value['operator'];
                $connector = $value['connector'];
                if (is_array($searched)) {
                    $searched = implode(',', $searched);
                }

                if ($operator == DataSet::OPERATOR_EQUAL)
                    $res = $res && ($row[$field] == $searched);
                elseif ($operator == DataSet::OPERATOR_NOT_EQUAL)
                    $res = $res && ($row[$field] <> $searched);
                elseif ($operator == DataSet::OPERATOR_GREATER)
                    $res = $res && ($row[$field] > $searched);
                elseif ($operator == DataSet::OPERATOR_GREATER_EQUAL)
                    $res = $res && ($row[$field] >= $searched);
                elseif ($operator == DataSet::OPERATOR_LESS)
                    $res = $res && ($row[$field] < $searched);
                elseif ($operator == DataSet::OPERATOR_LESS_EQUAL)
                    $res = $res && ($row[$field] <= $searched);
                elseif ($operator == DataSet::OPERATOR_BEGIN)
                    $res = $res && (strpos($row[$field], $searched) === 0);
                elseif ($operator == DataSet::OPERATOR_NOT_BEGIN)
                    $res = $res && !(strpos($row[$field], $searched) === 0);
                elseif ($operator == DataSet::OPERATOR_END)
                    $res = $res && (strpos($row[$field], $searched) === (strlen($row[$field]) - strlen($searched) - 1));
                elseif ($operator == DataSet::OPERATOR_NOT_END)
                    $res = $res && !(strpos($row[$field], $searched) === (strlen($row[$field]) - strlen($searched) - 1));
                elseif ($operator == DataSet::OPERATOR_CONTAIN)
                    $res = $res && (strpos($row[$field], $searched) !== false);
                elseif ($operator == DataSet::OPERATOR_NOT_CONTAIN)
                    $res = $res && (strpos($row[$field], $searched) === false);
                elseif ($operator == DataSet::OPERATOR_IN)
                    $res = $res && (in_array($row[$field], $searched));
                elseif ($operator == DataSet::OPERATOR_NOT_IN)
                    $res = $res && !(in_array($row[$field], $searched));
                elseif ($operator == DataSet::OPERATOR_IS_NULL)
                    $res = $res && (is_null($row[$field]));
                elseif ($operator == DataSet::OPERATOR_IS_NOT_NULL)
                    $res = $res && !(is_null($row[$field]));
            }
        }

        return $res;
    }

}
