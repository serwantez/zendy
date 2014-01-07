<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Msg;
use ZendY\Exception;
use ZendY\Css;

/**
 * Zbiór danych przechowujący struktury drzewiaste (Nested Set)
 *
 * @author Piotr Zając
 */
class NestedTree extends Table implements TreeSetInterface {
    /**
     * Kolumny zbioru
     */

    const COL_LFT = 'lft';
    const COL_RGT = 'rgt';
    /**
     * Tryby dodawania rekordu
     */
    const INSERT_AFTER = 1;
    const INSERT_BEFORE = 2;
    const INSERT_UNDER = 3;
    /**
     * Tryby wstawiania rekordu
     */
    const PASTE_AFTER = 1;
    const PASTE_BEFORE = 2;
    const PASTE_UNDER = 3;
    /**
     * Akcje
     */
    const ACTION_ADDBEFORE = 'addBeforeAction';
    const ACTION_ADDUNDER = 'addUnderAction';
    const ACTION_CUT = 'cutAction';
    const ACTION_PASTEBEFORE = 'pasteBeforeAction';
    const ACTION_PASTEUNDER = 'pasteUnderAction';
    const ACTION_PASTEAFTER = 'pasteAfterAction';

    /**
     * Nazwa pola przechowującego wartość "z lewej"
     * 
     * @var string
     */
    protected $_leftField = self::COL_LFT;

    /**
     * Nazwa pola przechowującego wartość "z prawej"
     * 
     * @var string
     */
    protected $_rightField = self::COL_RGT;

    /**
     * Nazwa pola przechowującego wartość "głębokości zagnieżdżenia"
     * 
     * @var string
     */
    protected $_depthField = 'depth';

    /**
     * Nazwa pola przechowującego wartość "dziecko"
     * 
     * @var string
     */
    protected $_childrenField = 'children';

    /**
     * Nazwa pola przechowującego wartość "rodzic"
     * 
     * @var string
     */
    protected $_parentField = 'parent_';

    /**
     * Bieżący rekord przed akcją dodania nowego rekordu
     * 
     * @var array
     */
    protected $_insertRecord;

    /**
     * Rodzaj wstawiania nowego rekordu (za, przed, czy poniżej bieżącego)
     * 
     * @var int
     */
    protected $_insertType;

    /**
     * Bieżący rekord przed akcją jego przesunięcia
     * 
     * @var array
     */
    protected $_cutRecord;

    /**
     * Rodzaj wklejenia rekordu (za, przed, czy poniżej bieżącego)
     * 
     * @var int
     */
    protected $_pasteType;

    /**
     * Ustawia parametry akcji
     * 
     * @return \ZendY\Db\DataSet\NestedTree
     */
    protected function _registerActions() {
        parent::_registerActions();

        $this->_registerAction(
                self::ACTION_ADDBEFORE
                , self::ACTIONTYPE_EDIT
                , array('primary' => Css::ICON_PLUS)
                , 'Add before'
                , null
                , false
                , self::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_ADDUNDER
                , self::ACTIONTYPE_EDIT
                , array('primary' => Css::ICON_PLUS)
                , 'Add under'
                , null
                , false
                , self::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_CUT
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SCISSORS)
                , 'Cut'
                , 'Ctrl+Shift+X'
                , false
                , self::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_PASTEUNDER
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_ARROWRETURN1E)
                , 'Paste under'
                , null
                , true
                , self::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_PASTEBEFORE
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_ARROWRETURN1S)
                , 'Paste before'
                , null
                , true
                , self::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_PASTEAFTER
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_ARROWREFRESH1S)
                , 'Paste after'
                , 'Ctrl+Shift+V'
                , true
                , self::ACTION_PRIVILEGE_EDIT
        );

        return $this;
    }

    /**
     * Ustawia stan przycisków nawigacyjnych
     * 
     * @return NestedTree 
     */
    protected function _setActionState($params = array()) {
        parent::_setActionState($params);
        $cur = $this->getCurrent();
        //zbiór musi być w trybie edycji, nie może być tylko do odczytu
        $this->_navigator[self::ACTION_ADD] = (
                $this->_navigator[self::ACTION_ADD]
                && $cur[$this->_leftField] > 1);
        $this->_navigator[self::ACTION_ADDBEFORE] = (
                $this->_state == self::STATE_VIEW
                && !$this->_readOnly
                && $this->_recordCount > 0
                && $cur[$this->_leftField] > 1);
        $this->_navigator[self::ACTION_ADDUNDER] = (
                $this->_state == self::STATE_VIEW
                && !$this->_readOnly
                && $this->_recordCount > 0);
        $this->_navigator[self::ACTION_CUT] = (
                $this->_state == self::STATE_VIEW
                && !$this->_readOnly
                && $cur[$this->_leftField] > 1);
        $this->_navigator[self::ACTION_PASTEBEFORE] = (
                $this->_state == self::STATE_VIEW
                && !$this->_readOnly
                && $cur[$this->_leftField] > 1
                && isset($this->_cutRecord)
                );
        $this->_navigator[self::ACTION_PASTEAFTER] = $this->_navigator[self::ACTION_PASTEBEFORE];
        $this->_navigator[self::ACTION_PASTEUNDER] = (
                $this->_state == self::STATE_VIEW
                && !$this->_readOnly
                && isset($this->_cutRecord)
                );
        return $this;
    }

    /**
     * Ustawia pole przechowujące wartość "z lewej"
     * 
     * @param string $left
     * @return \ZendY\Db\DataSet\NestedTree
     */
    public function setLeftField($left) {
        $this->_leftField = (string) $left;
        return $this;
    }

    /**
     * Ustawia pole przechowujące wartość "z prawej"
     *
     * @param string $right
     * @return \ZendY\Db\DataSet\NestedTree
     */
    public function setRightField($right) {
        $this->_rightField = (string) $right;
        return $this;
    }

    /**
     * Ustawia pole przechowujące wartość "dziecko"
     *
     * @param string $children
     * @return \ZendY\Db\DataSet\NestedTree
     */
    public function setChildrenField($children) {
        $this->_childrenField = (string) $children;
        return $this;
    }

    /**
     * Ustawia pole przechowujące wartość "głębokości zagnieżdżenia"
     *
     * @param string $depth
     * @return \ZendY\Db\DataSet\NestedTree
     */
    public function setDepthField($depth) {
        $this->_depthField = (string) $depth;
        return $this;
    }

    /**
     * Zwraca pole przechowujące wartość "z lewej"
     * 
     * @return string
     */
    public function getLeftField() {
        return $this->_leftField;
    }

    /**
     * Zwraca pole przechowujące wartość "z prawej"
     * 
     * @return string
     */
    public function getRightField() {
        return $this->_rightField;
    }

    /**
     * Zwraca pole przechowujące wartość "dziecko"
     * 
     * @return string
     */
    public function getChildrenField() {
        return $this->_childrenField;
    }

    /**
     * Zwraca pole przechowujące wartość "głębokości zagnieżdżenia"
     * 
     * @return string
     */
    public function getDepthField() {
        return $this->_depthField;
    }

    /**
     * Zwraca nazwy pól rodzica dla zapytania sql
     * 
     * @return array
     * @throws Exception
     */
    protected function _getParentFields() {
        $parentFields = array();
        $primary = $this->getPrimary();
        if (is_array($primary) && count($primary) > 1) {
            throw new Exception('NestedTree only accepts single primary key');
        } else {
            foreach ($primary as $key => $field) {
                $parentFields[$this->_parentField . $field] = $field;
            }
        }
        return $parentFields;
    }

    /**
     * Zwraca obiekt zapytania o drzewo
     * 
     * @param int|null $root
     * @param array|null $columns
     * @return \Zend_Db_Select
     */
    protected function _getTreeSelect($root = null, $columns = array(), $details = true) {
        $getDepth = false;
        $getParent = false;

        if (!isset($columns) || !count($columns)) {
            $columns = array("*");
            $getDepth = TRUE;
            $getParent = TRUE;
        }
        if (in_array($this->_depthField, $columns)) {
            $getDepth = TRUE;
            unset($columns[array_search($this->_depthField, $columns)]);
        }
        if (in_array($this->_parentField, $columns)) {
            $getParent = TRUE;
            unset($columns[array_search($this->_parentField, $columns)]);
            //$columns[$this->_parentField] = $this->_parentField;
        }
        
        if (!$details) {
            $getParent = false;
            $getDepth = false;
        }

        if ($getDepth) {
            //podzapytanie zliczające głębokość elementu drzewa
            $depthSubq = new \Zend_Db_Expr('(' . $this->_db->select()
                                    ->from(array("d" => $this->_name), "count(*)-1")
                                    ->where("node.$this->_leftField BETWEEN d.$this->_leftField AND d.$this->_rightField") . ')');

            $columns[$this->_depthField] = $depthSubq;
        }

        $q = $this->_db->select()
                ->from(array("node" => $this->_name), $columns);

        //dodaje pola rodzica
        if ($getParent) {
            $primary = $this->getPrimary();
            //podzapytanie obliczające id rodzica
            $i = 0;
            $cond = '';
            foreach ($primary as $field) {
                $parentSubq = new \Zend_Db_Expr('(' . $this->_db->select()
                                        ->from(array("sp" => $this->_name), $field)
                                        ->where("sp.$this->_leftField < node.$this->_leftField AND sp.$this->_rightField > node.$this->_rightField")
                                        ->order(new \Zend_Db_Expr("sp.$this->_rightField - node.$this->_rightField"))
                                        ->limit(1) . ')');
                if ($i > 0)
                    $cond .= " and ";
                $cond .= $parentSubq . " = `p`.`" . $field . "`";
                $i++;
            }
            $q->joinLeft(array('p' => $this->_name), $cond, $this->_getParentFields());
        }

        //drzewo od wskazanego miejsca (wybrana gałąź)
        if (isset($root)) {
            $left = new \Zend_Db_Expr('(' . $this->_db->select()
                                    ->from($this->_name, $this->_leftField)
                                    ->where("$this->_primary = ?", $root) . ')');
            $right = new \Zend_Db_Expr('(' . $this->_db->select()
                                    ->from($this->_name, $this->_rightField)
                                    ->where("$this->_primary = ?", $root) . ')');
            $q->where("node.$this->_leftField > $left AND node.$this->_leftField < $right");
        }

        //filtry zewnętrzne
        if (count($this->_filter->getFilters())) {
            $q->where($this->_filter->toSelect());
        }

        $q->order(array("node.$this->_leftField"));

        return $q;
    }

    /**
     * Zwraca rekordy w postaci tablicy dwuwymiarowej
     * 
     * @param int|null $root
     * @param array|null $columns
     * @return array
     */
    protected function _getTree($root = null, $columns = array()) {
        $data = array();
        $select = $this->_getTreeSelect($root, $columns);
        try {
            $data = $this->_db->fetchAll($select);
        } catch (Exception $exc) {
            echo $select . '<br />' . $exc->getTraceAsString();
        }
        if (is_object($data))
            $data = $data->toArray();

        return $data;
    }

    /**
     * Zwraca dane w postaci tablicy dwuwymiarowej
     * 
     * @param int|null $offset
     * @param int|null $itemCount
     * @param null|array|string $column
     * @param null|array $conditionalFormats
     * @return array 
     */
    public function getItems($offset = null, $itemCount = null, $columns = null, $conditionalFormats = null) {
        return $this->_getTree(null, $columns);
    }

    /**
     * Zwraca bieżący rekord
     * 
     * @return array
     */
    public function getCurrent($filterBlobs = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        if ($this->_state && $this->_offset >= 0) {
            $select = $this->_getTreeSelect(null, array('*'));
            $select->limit(1, $this->_offset);
            //exit($select);
            $q = $select->query();
            Msg::add($this->getId() . '->' . __FUNCTION__.' after query');
            $row = $q->fetch(\PDO::FETCH_ASSOC);
            //$row = $this->_db->fetchRow($select);
            //return $this->getAdapter()->fetchRow($select);
        } else {
            $row = $this->_table->createRow()->toArray();
        }
        if ($filterBlobs) {
            $blobs = $this->getBlobFields();
            foreach ($blobs as $field) {
                $row[$field] = 'blob';
            }
        }
        return $row;
    }

    /**
     * Pobiera pierwszy wiersz podanej kolumny
     * 
     * @param string $col
     * @param string|null $where
     * @param mixed|null $order
     * @return string 
     */
    public function fetchOne($col, $where = null, $order = null) {
        $select = $this->_getTreeSelect(null, array($col));

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
     * Tworzy tablicę hierarchiczną (wielowymiarową)
     * 
     * @param array $collection
     * @return array
     */
    protected function _createMultiDimensionalArray(array $collection) {
        // Trees mapped
        $trees = array();
        $l = 0;

        if (count($collection) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = array();

            foreach ($collection as $node) {
                $item = $node;
                //$item[$this->_childrenField] = array();
                // Number of stack items
                $l = count($stack);

                // Check if we're dealing with different levels
                while ($l > 0 && $stack[$l - 1][$this->_depthField] >= $item[$this->_depthField]) {
                    array_pop($stack);
                    $l--;
                }

                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root node
                    $i = count($trees);
                    $trees[$i] = $item;
                    $stack[] = & $trees[$i];
                } else {
                    // Add node to parent
                    if (!array_key_exists($this->_childrenField, $stack[$l - 1]))
                        $stack[$l - 1][$this->_childrenField] = array();
                    $i = count($stack[$l - 1][$this->_childrenField]);
                    $stack[$l - 1][$this->_childrenField][$i] = $item;
                    $stack[] = & $stack[$l - 1][$this->_childrenField][$i];
                }
            }
        }
        return $trees;
    }

    /**
     * Przekształca tablicę dwuwymiarową w tablicę hierarchiczną (wielowymiarową)
     * 
     * @param int|null $root
     * @return array 
     */
    public function toMultiDimensionalArray($root = null) {
        $data = $this->_getTree($root);
        return $this->_createMultiDimensionalArray($data);
    }

    /**
     * Dodaje nowy rekord po rekordzie bieżącym
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function addAction($params = array(), $compositePart = false) {
        $cur = $this->getCurrent();
        $this->_insertRecord = $cur;
        $this->_insertType = self::INSERT_AFTER;
        return parent::addAction($compositePart);
    }

    /**
     * Dodaje nowy rekord przed rekordem bieżącym
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function addBeforeAction($params = array(), $compositePart = false) {
        $cur = $this->getCurrent();
        $this->_insertRecord = $cur;
        $this->_insertType = self::INSERT_BEFORE;
        return parent::addAction($compositePart);
    }

    /**
     * Dodaje nowy rekord pod rekordem bieżącym (nowa gałąź)
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function addUnderAction($params = array(), $compositePart = false) {
        $cur = $this->getCurrent();
        $this->_insertRecord = $cur;
        $this->_insertType = self::INSERT_UNDER;
        return parent::addAction($compositePart);
    }

    /**
     * Dodaje nowy rekord
     * 
     * @param array $data
     * @return mixed The primary key value(s), as an associative array if the 
     * key is compound, or a scalar if the key is single-column.
     */
    protected function _insert($data, $insertType = self::INSERT_AFTER) {
        //dane nowego rekordu
        switch ($insertType) {
            case self::INSERT_BEFORE:
                $data[$this->_leftField] = $this->_insertRecord[$this->_leftField];
                $data[$this->_rightField] = $this->_insertRecord[$this->_leftField] + 1;
                break;
            case self::INSERT_UNDER:
                $data[$this->_leftField] = $this->_insertRecord[$this->_leftField] + 1;
                $data[$this->_rightField] = $this->_insertRecord[$this->_leftField] + 2;
                break;
            case self::INSERT_AFTER:
            default:
                $data[$this->_leftField] = $this->_insertRecord[$this->_rightField] + 1;
                $data[$this->_rightField] = $this->_insertRecord[$this->_rightField] + 2;
                break;
        }
        $this->_db->beginTransaction();
        try {
            switch ($insertType) {
                case self::INSERT_BEFORE:
                    $this->_table->update(array(
                        $this->_leftField => (
                        new \Zend_Db_Expr($this->_leftField . ' + 2')))
                            , $this->_leftField . ' >= ' . $this->_insertRecord[$this->_leftField]
                    );
                    $this->_table->update(array(
                        $this->_rightField => (
                        new \Zend_Db_Expr($this->_rightField . ' + 2')))
                            , $this->_rightField . ' > ' . $this->_insertRecord[$this->_leftField]
                    );
                    break;
                case self::INSERT_UNDER:
                    $this->_table->update(array($this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' + 2'))), $this->_leftField . ' > ' . $this->_insertRecord[$this->_leftField]);
                    $this->_table->update(array($this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' + 2'))), $this->_rightField . ' > ' . $this->_insertRecord[$this->_leftField]);
                    break;
                case self::INSERT_AFTER:
                default:
                    $this->_table->update(array($this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' + 2'))), $this->_leftField . ' > ' . $this->_insertRecord[$this->_rightField]);
                    $this->_table->update(array($this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' + 2'))), $this->_rightField . ' > ' . $this->_insertRecord[$this->_rightField]);
                    break;
            }
            //zapisanie nowego rekordu
            $result = $this->_table->createRow($data)->save();
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            $result = $e;
        }
        return $result;
    }

    /**
     * Dodaje pierwszy rekord (wierzchołek drzewa)
     * 
     * @param array $data
     * @return mixed The primary key value(s), as an associative array if the
     * key is compound, or a scalar if the key is single-column.
     */
    protected function _insertRoot($data) {
        $data[$this->_leftField] = 1;
        $data[$this->_rightField] = 2;
        return $this->_table->createRow($data)->save();
    }

    /**
     * Zwraca wartości pól klucza głównego w bieżącym rekordzie
     * 
     * @return array|null
     */
    public function getPrimaryValue() {
        if ($this->_state && $this->_offset >= 0) {
            $primary = $this->getPrimary();

            $select = $this->_getTreeSelect();
            $select->limit(1, $this->_offset);
            $record = $this->_db->fetchRow($select);
            $values = array();

            if (isset($record)) {
                foreach ($primary as $primaryColumn) {
                    $values[$primaryColumn] = $record[$primaryColumn];
                }
                return $values;
            }
        }
        return null;
    }

    /**
     * Zwraca opis kolumn tabeli w postaci tablicy asocjacyjnej, 
     * gdzie kluczami są nazwy kolumn
     * 
     * @return array
     */
    public function describe() {
        return $this->_db->describeTable($this->_name);
    }

    /**
     * Zapisuje zmiany w bieżącym wierszu
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array 
     */
    public function saveAction($params = array(), $compositePart = false) {
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

                //dane tabeli
                $describe = $this->describe();
                $data = $params['fieldsValues'];

                foreach ($data as $key => $value) {
                    //zabezpieczenie przed próbą błędnego zapisania tablicy do pola
                    if (is_array($value)) {
                        //$data[$key] = implode(";", $value);
                        $data[$key] = $value[0];
                    }
                    $col = $describe[$key];
                    if (in_array($col['DATA_TYPE'], array('blob', 'tinyblob', 'mediumblob', 'longblob'))) {
                        $path = pathinfo($data[$key]);
                        $url = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . \Blueimp\Upload\Handler::$uploadDir . $path['basename'];
                        if (file_exists($url)) {
                            $data[$key] = file_get_contents($url);
                            unlink($url);
                        }
                        else
                            unset($data[$key]);
                    }
                }

                if ($this->_state == self::STATE_EDIT) {
                    $this->_update($data);
                    $ret = $this->getPrimaryValue();
                } elseif ($this->_state == self::STATE_INSERT) {
                    if ($this->_recordCount == 0)
                        $ret = $this->_insertRoot($data);
                    else
                        $ret = $this->_insert($data, $this->_insertType);
                    $this->_recordCount = $this->_count();
                }
                $this->_state = self::STATE_VIEW;

                $primaryKey = $this->getPrimary();
                foreach ($primaryKey as $key) {
                    $searchValues[$key] = is_array($ret) ? $ret[$key] : $ret;
                }
                $result = array_merge($result, $this->searchAction(array('searchValues' => $searchValues), true));
                if (!$compositePart) {
                    $this->_setActionState();
                }
            }
        } else {
            $result['messages'] = $messages;
        }
        return $result;
    }

    /**
     * Usuwa rekord
     * 
     * @return array
     */
    protected function _delete() {
        $result = array();

        $cur = $this->getCurrent();
        $width = $cur[$this->_rightField] - $cur[$this->_leftField] + 1;
        $this->_db->beginTransaction();
        try {
            //aktualizacja rekordów przesuniętych "w lewo"
            $this->_table->delete($this->_leftField . ' between ' . $cur[$this->_leftField] . ' and ' . $cur[$this->_rightField]);
            $this->_table->update(array($this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' - ' . $width))), $this->_leftField . ' > ' . $cur[$this->_rightField]);
            $this->_table->update(array($this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' - ' . $width))), $this->_rightField . ' > ' . $cur[$this->_rightField]);
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            $result[] = $e;
        }
        return $result;
    }

    /**
     * Akcja usuwająca element drzewa
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array 
     */
    public function deleteAction($params = array(), $compositePart = false) {
        $result = array();
        $result = $this->_delete();
        $this->_recordCount = $this->_count();
        if ($this->_offset >= $this->_recordCount && $this->_recordCount > 0)
            $this->_offset = $this->_recordCount - 1;
        $this->_state = self::STATE_VIEW;
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Akcja "wycinająca" rekord przed przeniesieniem go do innej pozycji
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function cutAction($params = array(), $compositePart = false) {
        $result = array();
        $cur = $this->getCurrent();
        $this->_cutRecord = $cur;
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Akcja wklejająca rekord wycięty przed bieżącym
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function pasteBeforeAction($params = array(), $compositePart = false) {
        $this->_pasteType = self::PASTE_BEFORE;
        $result = $this->_paste();
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Akcja wklejająca rekord wycięty za bieżącym
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function pasteAfterAction($params = array(), $compositePart = false) {
        $this->_pasteType = self::PASTE_AFTER;
        $result = $this->_paste();
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Akcja wklejająca rekord wycięty pod bieżącym
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function pasteUnderAction($params = array(), $compositePart = false) {
        $this->_pasteType = self::PASTE_UNDER;
        $result = $this->_paste();
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Przenosi gałąź do wskazanego miejsca
     * 
     * @return array
     */
    protected function _paste() {
        $result = array();
        //rekord bieżący przy wycinaniu
        $c = $this->_cutRecord;
        //rekord bieżący przy wklejaniu
        $p = $this->getCurrent();
        $this->_db->beginTransaction();
        try {
            switch ($this->_pasteType) {
                //wkleja pod bieżącym rekordem (na końcu gałęzi)
                case self::PASTE_UNDER:
                    //przeniesienie "do przodu"
                    if ($p[$this->_leftField] > $c[$this->_leftField]) {
                        //wartość przesunięcia rekordów w prawo
                        $dr = $p[$this->_rightField] - $c[$this->_rightField] - 1;
                        //wartość przesunięcia rekordów w lewo (ujemna)
                        $dl = $c[$this->_leftField] - $c[$this->_rightField] - 1;

                        $result[] = $this->_table->update(
                                array(
                            $this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' + case 
                                when ' . $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . ($c[$this->_rightField] - 1) . ' 
                                then ' . $dr . ' 
                                when ' . $this->_leftField . ' between ' . ($c[$this->_rightField] + 1) . ' and ' . ($p[$this->_rightField] - 1) . ' 
                                then ' . $dl . ' 
                                else 0
                                end'
                            )),
                            $this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' + case 
                                when ' . $this->_rightField . ' between ' . ($c[$this->_leftField] + 1) . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dr . ' 
                                when ' . $this->_rightField . ' between ' . ($c[$this->_rightField] + 1) . ' and ' . ($p[$this->_rightField] - 1) . ' 
                                then ' . $dl . ' 
                                else 0 
                                end'
                            ))
                                ), $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . $p[$this->_rightField] . ' or ' .
                                $this->_rightField . ' between ' . $c[$this->_leftField] . ' and ' . $p[$this->_rightField]
                        );
                    } else {
                        //przeniesienie "do tyłu"
                        //wartość przesunięcia rekordów w prawo
                        $dr = $c[$this->_rightField] - $c[$this->_leftField] + 1;
                        //wartość przesunięcia rekordów w lewo (ujemna)
                        $dl = $p[$this->_rightField] - $c[$this->_leftField];

                        $result[] = $this->_table->update(
                                array(
                            $this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' + case 
                                when ' . $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dl . ' 
                                when ' . $this->_leftField . ' between ' . $p[$this->_rightField] . ' and ' . ($c[$this->_leftField] - 1) . ' 
                                then ' . $dr . ' 
                                else 0
                                end'
                            )),
                            $this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' + case 
                                when ' . $this->_rightField . ' between ' . $c[$this->_leftField] . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dl . ' 
                                when ' . $this->_rightField . ' between ' . $p[$this->_rightField] . ' and ' . ($c[$this->_leftField] - 1) . ' 
                                then ' . $dr . ' 
                                else 0 
                                end'
                            ))
                                ), $this->_leftField . ' between ' . $p[$this->_rightField] . ' and ' . $c[$this->_rightField] . ' or ' .
                                $this->_rightField . ' between ' . $p[$this->_rightField] . ' and ' . $c[$this->_rightField]
                        );
                    }
                    break;
                //wkleja przed bieżącym rekordem
                case self::PASTE_BEFORE:
                    //przeniesienie "do przodu"
                    if ($p[$this->_leftField] > $c[$this->_leftField]) {
                        //wartość przesunięcia rekordów w prawo
                        $dr = $p[$this->_leftField] - $c[$this->_rightField] - 1;
                        //wartość przesunięcia rekordów w lewo (ujemna)
                        $dl = $c[$this->_leftField] - $c[$this->_rightField] - 1;

                        $result[] = $this->_table->update(
                                array(
                            $this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' + case 
                                when ' . $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . ($c[$this->_rightField] - 1) . ' 
                                then ' . $dr . ' 
                                when ' . $this->_leftField . ' between ' . ($c[$this->_rightField] + 1) . ' and ' . ($p[$this->_leftField] - 1) . ' 
                                then ' . $dl . ' 
                                else 0
                                end'
                            )),
                            $this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' + case 
                                when ' . $this->_rightField . ' between ' . ($c[$this->_leftField] + 1) . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dr . ' 
                                when ' . $this->_rightField . ' between ' . ($c[$this->_rightField] + 1) . ' and ' . ($p[$this->_leftField] - 1) . ' 
                                then ' . $dl . ' 
                                else 0 
                                end'
                            ))
                                ), $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . ($p[$this->_leftField] - 1) . ' or ' .
                                $this->_rightField . ' between ' . $c[$this->_leftField] . ' and ' . ($p[$this->_leftField] - 1)
                        );
                    } else {
                        //przeniesienie "do tyłu"
                        //wartość przesunięcia rekordów w prawo
                        $dr = $c[$this->_rightField] - $c[$this->_leftField] + 1;
                        //wartość przesunięcia rekordów w lewo (ujemna)
                        $dl = $p[$this->_leftField] - $c[$this->_leftField];

                        $result[] = $this->_table->update(
                                array(
                            $this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' + case 
                                when ' . $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . ($c[$this->_rightField] - 1) . ' 
                                then ' . $dl . ' 
                                when ' . $this->_leftField . ' between ' . $p[$this->_leftField] . ' and ' . ($c[$this->_leftField] - 1) . ' 
                                then ' . $dr . ' 
                                else 0 
                                end'
                            )),
                            $this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' + case 
                                when ' . $this->_rightField . ' between ' . ($c[$this->_leftField] + 1) . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dl . ' 
                                when ' . $this->_rightField . ' between ' . ($p[$this->_leftField] + 1) . ' and ' . ($c[$this->_leftField] - 1) . ' 
                                then ' . $dr . ' 
                                else 0 
                                end'
                            ))
                                ), $this->_leftField . ' between ' . $p[$this->_leftField] . ' and ' . $c[$this->_rightField] . ' or ' .
                                $this->_rightField . ' between ' . $p[$this->_leftField] . ' and ' . $c[$this->_rightField]
                        );
                    }
                    break;
                //wkleja po bieżącym rekordzie
                case self::PASTE_AFTER:
                default:
                    //przeniesienie "do przodu"
                    if ($p[$this->_rightField] > $c[$this->_leftField]) {
                        //wartość przesunięcia rekordów w prawo
                        $dr = $p[$this->_rightField] - $c[$this->_rightField];
                        //wartość przesunięcia rekordów w lewo (ujemna)
                        $dl = $c[$this->_leftField] - $c[$this->_rightField] - 1;

                        $result[] = $this->_table->update(
                                array(
                            $this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' + case 
                                when ' . $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dr . ' 
                                when ' . $this->_leftField . ' between ' . ($c[$this->_rightField] + 1) . ' and ' . $p[$this->_rightField] . ' 
                                then ' . $dl . ' 
                                else 0 
                                end'
                            )),
                            $this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' + case 
                                when ' . $this->_rightField . ' between ' . ($c[$this->_leftField] + 1) . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dr . ' 
                                when ' . $this->_rightField . ' between ' . ($c[$this->_rightField] + 1) . ' and ' . $p[$this->_rightField] . ' 
                                then ' . $dl . ' 
                                else 0 
                                end'
                            ))
                                ), $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . $p[$this->_rightField] . ' or ' .
                                $this->_rightField . ' between ' . ($c[$this->_leftField] + 1) . ' and ' . $p[$this->_rightField]
                        );
                    } else {
                        //przeniesienie "do tyłu"
                        //wartość przesunięcia rekordów w prawo
                        $dr = $c[$this->_rightField] - $c[$this->_leftField] + 1;
                        //wartość przesunięcia rekordów w lewo (ujemna)
                        $dl = $p[$this->_rightField] - $c[$this->_leftField] + 1;
                        $result[] = $this->_table->update(
                                array(
                            $this->_leftField => (new \Zend_Db_Expr($this->_leftField . ' + case 
                                when ' . $this->_leftField . ' between ' . $c[$this->_leftField] . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dl . ' 
                                when ' . $this->_leftField . ' between ' . ($p[$this->_rightField] + 1) . ' and ' . ($c[$this->_leftField] - 1) . ' 
                                then ' . $dr . ' 
                                else 0  
                                end'
                            )),
                            $this->_rightField => (new \Zend_Db_Expr($this->_rightField . ' + case 
                                when ' . $this->_rightField . ' between ' . $c[$this->_leftField] . ' and ' . $c[$this->_rightField] . ' 
                                then ' . $dl . ' 
                                when ' . $this->_rightField . ' between ' . ($p[$this->_rightField] + 1) . ' and ' . ($c[$this->_leftField] - 1) . ' 
                                then ' . $dr . ' 
                                else 0 
                                end'
                            ))
                                ), $this->_leftField . ' between ' . $p[$this->_leftField] . ' and ' . $c[$this->_rightField] . ' or ' .
                                $this->_rightField . ' between ' . $p[$this->_leftField] . ' and ' . $c[$this->_rightField]
                        );
                    }
                    break;
            }
            $this->_db->commit();
            unset($c);
        } catch (Exception $e) {
            $this->_db->rollBack();
            $result[] = $e;
        }
        return $result;
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
            $select = $this->_getTreeSelect(null, null, FALSE);
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
                $filter->addFilter('node.' . $field, $searched, $operator);
            }
            $select->where($filter->toSelect());

            try {
                //print($select);
                $q = $select->query();
                $array = $q->fetchAll();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                exit($select);
            }
            if (!is_array($array)) {
                $array = $array->toArray();
            }
            $where = "node." . $this->_leftField . " <= '" . $array[0][$this->_leftField] . "'";
            $select2->reset(\ZendY\Db\Select::COLUMNS);
            $select2->columns("count(*)-1")
                    ->where($where);
            try {
                //print($select2);
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

}