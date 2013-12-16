<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Css;

/**
 * Klasa bazowa zbioru danych
 *
 * @author Piotr Zając
 */
abstract class Editable extends Base {

    use EditableTrait;

    /*
     * Akcje na zbiorze
     */

    const ACTION_ADD = 'addAction';
    const ACTION_CANCEL = 'cancelAction';
    const ACTION_ADDCOPY = 'addCopyAction';
    const ACTION_DELETE = 'deleteAction';
    const ACTION_EDIT = 'editAction';
    const ACTION_SAVE = 'saveAction';
    const ACTION_TRUNCATE = 'truncateAction';

    /**
     * Stan dodawania rekordu
     */
    const STATE_INSERT = 2;

    /**
     * Stan edycji rekordu
     */
    const STATE_EDIT = 3;

    /**
     * Ustawia parametry akcji
     * 
     * @return \ZendY\Db\DataSet\Editable
     */
    protected function _registerActions() {
        parent::_registerActions();

        $this->_registerAction(
                self::ACTION_ADD
                , self::ACTIONTYPE_EDIT
                , array('primary' => Css::ICON_PLUS)
                , 'Add'
                , NULL
                , false
                , Base::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_EDIT
                , self::ACTIONTYPE_EDIT
                , array('primary' => Css::ICON_PENCIL)
                , 'Edit'
                , NULL
                , false
                , Base::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_CANCEL
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_CANCEL)
                , 'Cancel'
                , NULL
                , false
                , Base::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_ADDCOPY
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_COPY)
                , 'Copy'
                , NULL
                , false
                , Base::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_SAVE
                , self::ACTIONTYPE_SAVE
                , array('primary' => Css::ICON_DISK)
                , 'Save'
                , NULL
                , true
                , Base::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_DELETE
                , self::ACTIONTYPE_CONFIRM
                , array('primary' => Css::ICON_TRASH)
                , 'Delete'
                , NULL
                , true
                , Base::ACTION_PRIVILEGE_DELETE
        );
        $this->_registerAction(
                self::ACTION_TRUNCATE
                , self::ACTIONTYPE_CONFIRM
                , array('primary' => Css::ICON_TRASH)
                , 'Truncate'
                , NULL
                , true
                , Base::ACTION_PRIVILEGE_DELETE
        );

        return $this;
    }

    /**
     * Ustawia stan przycisków nawigacyjnych
     * 
     * @param array|null $params
     * @return \ZendY\Db\DataSet\Editable
     */
    protected function _setActionState($params = array()) {
        parent::_setActionState($params);
        $this->_navigator[self::ACTION_ADD] = ($this->_state == self::STATE_VIEW
                && !$this->_readOnly);
        $this->_navigator[self::ACTION_EDIT] = ($this->_state == self::STATE_VIEW
                && !$this->_readOnly
                && $this->_recordCount > 0);
        $this->_navigator[self::ACTION_CANCEL] = ($this->_state == self::STATE_EDIT
                || $this->_state == self::STATE_INSERT);
        $this->_navigator[self::ACTION_ADDCOPY] = $this->_navigator[self::ACTION_EDIT];
        $this->_navigator[self::ACTION_SAVE] = (($this->_state == self::STATE_INSERT
                || $this->_state == self::STATE_EDIT)
                && !$this->_readOnly);
        $this->_navigator[self::ACTION_DELETE] = (($this->_state == self::STATE_VIEW
                || $this->_state == self::STATE_EDIT)
                && $this->_recordCount > 0
                && !$this->_readOnly);
        $this->_navigator[self::ACTION_TRUNCATE] = (($this->_state == self::STATE_VIEW
                || $this->_state == self::STATE_EDIT)
                && !$this->_readOnly);

        return $this;
    }

    /** AKCJE */

    /**
     * Zapisuje zmiany w bieżącym rekordzie
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    abstract public function saveAction($params = array(), $compositePart = false);

    /**
     * Usuwa bieżący rekord
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    abstract public function deleteAction($params = array(), $compositePart = false);

    /**
     * Usuwa wszystkie rekordy
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    abstract public function truncateAction($params = array(), $compositePart = false);
}