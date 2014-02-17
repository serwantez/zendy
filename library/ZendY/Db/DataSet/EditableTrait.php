<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

/**
 * Cecha zbiorów edytowalnych
 *
 * @author Piotr Zając
 */
trait EditableTrait {

    /**
     * Ustawia stan przycisków nawigacyjnych
     * 
     * @param array|null $params
     * @return \ZendY\Db\DataSet\Editable
     */
    protected function _setActionState($params = array()) {
        parent::_setActionState($params);
        $this->_navigator[self::ACTION_ADD] = (
                $this->_state >= self::STATE_VIEW
                && !$this->_readOnly);
        $this->_navigator[self::ACTION_EDIT] = (
                $this->_state == self::STATE_VIEW
                && !$this->_readOnly
                && $this->_recordCount > 0);
        $this->_navigator[self::ACTION_CANCEL] = (
                $this->_state == self::STATE_EDIT
                || $this->_state == self::STATE_INSERT);
        $this->_navigator[self::ACTION_ADDCOPY] = $this->_navigator[self::ACTION_EDIT];
        $this->_navigator[self::ACTION_SAVE] = (
                ($this->_state == self::STATE_INSERT
                || $this->_state == self::STATE_EDIT)
                && !$this->_readOnly);
        $this->_navigator[self::ACTION_DELETE] = (
                ($this->_state == self::STATE_VIEW
                || $this->_state == self::STATE_EDIT)
                && $this->_recordCount > 0
                && !$this->_readOnly);
        $this->_navigator[self::ACTION_TRUNCATE] = (
                ($this->_state == self::STATE_VIEW
                || $this->_state == self::STATE_EDIT)
                && !$this->_readOnly);
        return $this;
    }

    /**
     * Zwraca informacje o pojedynczym polu tabeli źródłowej
     * 
     * @param string $field
     * @param array|null $describe
     * @return array|false
     */
    public function describeField($field, $describe = null) {
        if (!isset($describe)) {
            $describe = $this->describe();
        }
        if (array_key_exists($field, $describe)) {
            $result = $describe[$field];
        } else
            $result = false;
        return $result;
    }

    /**
     * Przechodzi do trybu dodawania rekordu
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function addAction($params = array(), $compositePart = false) {
        $result = array();
        $this->_state = self::STATE_INSERT;
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Odwołuje zapisywanie
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function cancelAction($params = array(), $compositePart = false) {
        $result = array();
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
     * Kopiuje dane bieżącego rekordu i przechodzi w tryb dodawania rekordu
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function addCopyAction($params = array(), $compositePart = false) {
        $result = array();
        $this->_state = self::STATE_INSERT;
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Przechodzi do trybu edycji rekordu
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function editAction($params = array(), $compositePart = false) {
        $result = array();
        $this->_state = self::STATE_EDIT;
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

}
