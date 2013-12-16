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
