<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Exception;
use ZendY\Css;

/**
 * Zbiór danych z kolumną sortującą
 *
 * @author Piotr Zając
 */
class Sortable extends Table {
    /*
     * Akcje na zbiorze
     */

    const ACTION_MOVETO = 'moveToAction';

    /**
     * Nazwa pola przechowującego wartość informującą o kolejności rekordów na liście
     * 
     * @var string
     */
    protected $_sortField = 'sort';

    /**
     * Ustawia parametry akcji
     * 
     * @return \ZendY\Db\DataSet\Sortable
     */
    protected function _registerActions() {
        parent::_registerActions();

        /**
         * @todo Actions moveUp and moveDown
         */
        $this->_registerAction(
                self::ACTION_MOVETO
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_ARROWTHICK2NS)
                , 'Move to'
                , NULL
                , true
                , self::ACTION_PRIVILEGE_EDIT
        );

        return $this;
    }

    /**
     * Ustawia stan przycisków nawigacyjnych
     * 
     * @param array|null $params
     * @return \ZendY\Db\DataSet\Sortable
     */
    protected function _setActionState($params = array()) {
        parent::_setActionState($params);
        $this->_navigator[self::ACTION_MOVETO] = ($this->_state == self::STATE_VIEW && $this->_recordCount > 0 && !$this->_readOnly);

        return $this;
    }

    /**
     * Ustawia kolumnę sortującą
     * 
     * @param string $sortField
     * @return \ZendY\Db\DataSet\Sortable
     */
    public function setSortField($sortField) {
        $this->_sortField = $sortField;
        $this->sortAction(array('field' => $sortField));
        return $this;
    }

    /**
     * Zwraca kolumnę sortującą
     * @return string
     */
    public function getSortField() {
        return $this->_sortField;
    }

    /** AKCJE */

    /**
     * Przesuwa rekord do wskazanej pozycji
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function moveToAction($params = array(), $compositePart = false) {
        $result = array();
        if (isset($params['oldPosition']) && isset($params['newPosition'])) {
            $oldPosition = $params['oldPosition'] + 1;
            $newPosition = $params['newPosition'] + 1;
            $searchValues = $this->getPrimaryValue();
            $this->_db->beginTransaction();
            try {
                if ($oldPosition > $newPosition) {
                    $this->_table->update(array($this->_sortField => (new \Zend_Db_Expr('case 
                    when `' . $this->_sortField . '` between ' . $newPosition . ' and ' . ($oldPosition - 1) . '
                        then `' . $this->_sortField . '`+1
                        else ' . $newPosition . '
                        end'
                        )))
                            , '`' . $this->_sortField . '` between ' . $newPosition . ' and ' . $oldPosition
                            . ' and ' . $this->_filter->toSelect());
                } else {
                    $this->_table->update(array($this->_sortField => (new \Zend_Db_Expr('case 
                    when `' . $this->_sortField . '` between ' . ($oldPosition + 1) . ' and ' . $newPosition . ' 
                        then `' . $this->_sortField . '`-1
                        else ' . $newPosition . '
                        end'
                        )))
                            , '`' . $this->_sortField . '` between ' . $oldPosition . ' and ' . $newPosition
                            . ' and ' . $this->_filter->toSelect());
                }
                $this->_db->commit();
            } catch (Exception $e) {
                $this->_db->rollBack();
            }
            $result = array_merge($result, $this->searchAction(array('searchValues' => $searchValues), true));

            $this->_state = self::STATE_VIEW;

            if (!$compositePart) {
                $this->_setActionState();
            }
        }
        return $result;
    }

    /**
     * Zapisuje zmiany w bieżącym wierszu
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function saveAction($params = array(), $compositePart = false) {
        //nowy rekord zapisywany jest na końcu listy
        if ($this->_state == self::STATE_INSERT) {
            $params['fieldsValues'][$this->_sortField] = $this->_recordCount + 1;
        }
        $result = parent::saveAction($params, $compositePart);
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
        $cur = $this->getCurrent();
        $result = parent::deleteAction($params, $compositePart);
        //aktualizacja rekordów nastepujących po bieżącym
        $this->_table->update(array(
            $this->_sortField => (new \Zend_Db_Expr(
                    '`' . $this->_sortField . '`-1'
            )))
                , '`' . $this->_sortField . '` > ' . $cur[$this->_sortField]
                . ' and ' . $this->_filter->toSelect());
        return $result;
    }

}