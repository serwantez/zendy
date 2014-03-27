<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Db\DataSet\Base;

/**
 * Cecha tabel bazodanowych
 *
 * @author Piotr Zając
 */
trait TableTrait {

    /**
     * Nazwa tabeli
     * 
     * @var string
     */
    protected $_tableName;

    /**
     * Pomocniczy obiekt tabeli - do wykonywania operacji CUD
     * 
     * @var \Zend_Db_Table_Abstract
     */
    protected $_table;

    /**
     * Tablica definicji struktury tabeli
     * 
     * @var array
     */
    static public $tableDefs = array();

    /**
     * Zwraca instrukcję sql tworzącą tabelę
     * 
     * @param string $class Table class name
     * @return string
     */
    static public function getCreateTable($class) {
        if (!array_key_exists('fields', $class::$tableDefs)) {
            return false;
        }
        $sql = sprintf("CREATE TABLE IF NOT EXISTS `%s` ("
                , $class::$tableDefs['tableName']);
        foreach ($class::$tableDefs['fields'] as $key => $field) {
            if ($key > 0)
                $sql .= ", ";
            $sql .= sprintf("`%s` %s", $field['name'], $field['type']);
            if (array_key_exists('length', $field)) {
                $sql .= sprintf("(%s)", $field['length']);
            }
            if (array_key_exists('null', $field) && !$field['null']) {
                $sql .= " NOT NULL";
            }
            if (array_key_exists('default', $field)) {
                $sql .= sprintf(" DEFAULT %s"
                        , (($field['default'] == 'NULL' ||
                        $field['default'] == 'CURRENT_TIMESTAMP') ?
                                $field['default'] : "'" . $field['default'] . "'"));
            }
            if (array_key_exists('autoIncrement', $field) &&
                    $field['autoIncrement']) {
                $sql .= " AUTO_INCREMENT";
            }
        }

        if (array_key_exists('primaryKey', $class::$tableDefs)) {
            $sql .= ", PRIMARY KEY (";
            foreach ($class::$tableDefs['primaryKey'] as $key => $field) {
                if ($key > 0)
                    $sql .= ", ";
                $sql .= sprintf("`%s`", $field);
            }
            $sql .= ")";
        }

        if (array_key_exists('uniqueKey', $class::$tableDefs)) {
            foreach ($class::$tableDefs['uniqueKey'] as $name => $fields) {
                $sql .= sprintf(", UNIQUE KEY `%s` (", $name);
                foreach ($fields as $key => $field) {
                    if ($key > 0)
                        $sql .= ", ";
                    $sql .= sprintf("`%s`", $field);
                }
                $sql .= ")";
            }
        }

        if (array_key_exists('key', $class::$tableDefs)) {
            foreach ($class::$tableDefs['key'] as $name => $fields) {
                $sql .= sprintf(", KEY `%s` (", $name);
                foreach ($fields as $key => $field) {
                    if ($key > 0)
                        $sql .= ", ";
                    $sql .= sprintf("`%s`", $field);
                }
                $sql .= ")";
            }
        }

        $sql .= sprintf(") ENGINE = %s DEFAULT CHARSET = %s"
                , $class::$tableDefs['tableType']
                , $class::$tableDefs['tableCharset']);
        return $sql;
    }

    static public function getStartRecordsSQL($class) {
        if (method_exists($class, 'getStartRecords')) {
            $data = $class::getStartRecords();
            $sql = sprintf("INSERT INTO `%s` VALUES ", $class::$tableDefs['tableName']);
            foreach ($data as $key => $row) {
                if ($key > 0)
                    $sql .= ", ";
                $sql .= "(";
                $i = 0;
                foreach ($row as $field => $value) {
                    if ($i > 0)
                        $sql .= ", ";
                    if (isset($value))
                        $sql .= "'" . $value . "'";
                    else
                        $sql .= "null";
                    $i++;
                }
                $sql .= ")";
            }
            return $sql;
        }
        else
            return false;
    }

    /**
     * Ustawia nazwę tabeli
     * 
     * @param string $tableName
     */
    public function setTableName($tableName) {
        $this->_tableName = $tableName;
        $this->_table->setOptions(array(\Zend_Db_Table_Abstract::NAME => $tableName));
        return $this;
    }

    /**
     * Zwraca nazwę tabeli
     * 
     * @return string
     */
    public function getTableName() {
        if (isset($this->_tableName))
            return $this->_tableName;
        else
            return $this->_table->info(\Zend_Db_Table_Abstract::NAME);
    }

    /**
     * Usuwa rekord
     * 
     * @return array
     */
    protected function _delete() {
        $result = array();
        $where = '1=1';
        $pColumns = $this->getPrimary();
        $pValues = $this->getPrimaryValue();
        foreach ($pColumns as $column) {
            $where .= sprintf(' and %s = %s', $column, $pValues[$column]);
        }

        $this->_db->beginTransaction();
        try {
            if (!$this->_table->delete($where)) {
                $result[] = 'Delete function does not return a value';
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            $result[] = $e;
        }
        return $result;
    }

    /**
     * Akcja usuwająca rekord
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function deleteAction($params = array(), $compositePart = false) {
        $result = array();

        $this->beforeDeleteEvent($params);
        $result = $this->_delete();
        $this->afterDeleteEvent($params);
        $this->_recordCount = $this->_count();
        if ($this->_offset >= $this->_recordCount && $this->_recordCount > 0)
            $this->_offset--;
        if ($this->_editMode)
            $this->_state = self::STATE_EDIT;
        else
            $this->_state = self::STATE_VIEW;
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Usuwa wszystkie rekordy w tabeli
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function truncateAction($params = array(), $compositePart = false) {
        $result = array();
        if (!($truncate = $this->_db->query("TRUNCATE TABLE " . $this->_name)))
            $result[] = $truncate;
        $this->_recordCount = $this->_count();
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Zapisuje dane do bieżącego rekordu
     * 
     * @param array $data
     * @return int
     */
    protected function _update(array $data) {
        $where = '1=1';
        $pColumns = $this->getPrimary();
        $pValues = $this->getPrimaryValue();
        foreach ($pColumns as $column) {
            $where .= sprintf(' and %s = %s', $column, $pValues[$column]);
        }
        return $this->_table->update($data, $where);
    }

    /**
     * Obsługa filtrów z operatorem EQAUL działających jak widoki:
     * jeśli na danym polu jest filtr z operatorem EQUAL, 
     * a w formularzu brak kontrolki reprezentującej to pole, 
     * wtedy przy zapisie nowego rekordu pole przyjmie wartość filtra

     * @param array $data
     * @return array
     */
    protected function _saveOnFilter(array $data, $describe = null) {
        $filters = $this->getFilters();
        foreach ($filters as $filterName => $filter) {
            foreach ($filter as $field => $filterData) {
                //wyłuskanie domeny
                $dot = strpos($field, '.');
                if ($dot !== false) {
                    $domain = substr($field, 0, $dot);
                    $field = substr($field, $dot + 1);
                }
                if (!array_key_exists($field, $data)
                        && $filterData['operator'] == Base::OPERATOR_EQUAL
                        && $filterData['connector'] == Base::CONNECTOR_AND
                        && isset($describe[$field]['TABLE_NAME'])
                        && $describe[$field]['TABLE_NAME'] == $this->getTableName()) {
                    $data[$field] = $filterData['value'];
                }
            }
        }
        return $data;
    }

    /**
     * Zapisuje zmiany w bieżącym wierszu
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function saveAction($params = array(), $compositePart = false) {
        $this->beforeSaveEvent($params);
        $result = array();
        $ret = null;
        //walidacja formularza
        $form = $params['form'];
        if (is_string($form))
            $form = new $form();
        if (isset($params['elementsValues'])) {
            $form->isValidDataSource($params['elementsValues'], $params['id']);
        }
        $messages = \ZendY\Form::prepareFormMessages($form, $form->getMessages());
        if (count($messages) == 0) {
            if (isset($params['fieldsValues'])) {
                $ret = null;
                //dane kolumn zbioru
                $describe = $this->describe();

                $params['fieldsValues'] = $this->_saveOnFilter($params['fieldsValues'], $describe);

                foreach ($params['fieldsValues'] as $key => $value) {
                    //zabezpieczenie przed próbą błędnego zapisania tablicy do pola
                    if (is_array($value)) {
                        $params['fieldsValues'][$key] = $value[0];
                    }
                    //pola blob (grafika)
                    $col = $describe[$key];
                    if (in_array($col['DATA_TYPE'], array('blob', 'tinyblob', 'mediumblob', 'longblob'))) {
                        $path = pathinfo($params['fieldsValues'][$key]);
                        $url = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . \Blueimp\Upload\Handler::$uploadDir . $path['basename'];
                        if ($path['basename'] == 'empty') {
                            $params['fieldsValues'][$key] = '';
                        } elseif (file_exists($url)) {
                            $params['fieldsValues'][$key] = file_get_contents($url);
                            unlink($url);
                        } else
                            unset($params['fieldsValues'][$key]);
                    }
                }

                if ($this->_state == self::STATE_EDIT) {
                    $this->beforeUpdateEvent($params);
                    $this->_update($params['fieldsValues']);
                    $this->afterUpdateEvent($params);
                    if ($this->hasFilter()) {
                        $this->_recordCount = $this->_count();
                    }
                    $params['key'] = $this->getPrimaryValue();
                } elseif ($this->_state == self::STATE_INSERT) {
                    $this->beforeInsertEvent($params);
                    $params['key'] = $this->_table->createRow()->setFromArray($params['fieldsValues'])->save();
                    $this->afterInsertEvent($params);
                    $this->_recordCount = $this->_count();
                }
                if ($this->_editMode)
                    $this->_state = self::STATE_EDIT;
                else
                    $this->_state = self::STATE_VIEW;


                $primaryKey = $this->getPrimary();
                foreach ($primaryKey as $key) {
                    $searchValues[$key] = is_array($params['key']) && array_key_exists($key, $params['key']) ? $params['key'][$key] : $params['key'];
                }
                $result = array_merge($result, $this->searchAction(array('searchValues' => $searchValues), true));
                if (!$compositePart) {
                    $this->_setActionState();
                }
            }
        } else {
            $result['errors'] = $messages;
        }
        $this->afterSaveEvent($params);
        return $result;
    }

    /**
     * Zdarzenie wykonywane po zapisaniu rekordu
     * 
     * @param array $params
     * @return ZendY\Db\DataSet\TableTrait
     */
    public function afterSaveEvent(&$params) {
        return $this;
    }

    /**
     * Zdarzenie wykonywane przed zapisaniem rekordu
     * 
     * @param array $params
     * @return ZendY\Db\DataSet\TableTrait
     */
    public function beforeSaveEvent(&$params) {
        return $this;
    }

    /**
     * Zdarzenie wykonywane po aktualizacji rekordu
     * 
     * @param array $params
     * @return ZendY\Db\DataSet\TableTrait
     */
    public function afterUpdateEvent(&$params) {
        return $this;
    }

    /**
     * Zdarzenie wykonywane przed aktualizacją rekordu
     * 
     * @param array $params
     * @return ZendY\Db\DataSet\TableTrait
     */
    public function beforeUpdateEvent(&$params) {
        return $this;
    }

    /**
     * Zdarzenie wykonywane po dodaniu rekordu
     * 
     * @param array $params
     * @return ZendY\Db\DataSet\TableTrait
     */
    public function afterInsertEvent(&$params) {
        return $this;
    }

    /**
     * Zdarzenie wykonywane przed dodaniem rekordu
     * 
     * @param array $params
     * @return ZendY\Db\DataSet\TableTrait
     */
    public function beforeInsertEvent(&$params) {
        return $this;
    }

    /**
     * Zdarzenie wykonywane po usunięciu rekordu
     * 
     * @param array $params
     * @return ZendY\Db\DataSet\TableTrait
     */
    public function afterDeleteEvent(&$params) {
        return $this;
    }

    /**
     * Zdarzenie wykonywane przed usunięciem rekordu
     * 
     * @param array $params
     * @return ZendY\Db\DataSet\TableTrait
     */
    public function beforeDeleteEvent(&$params) {
        return $this;
    }

}
