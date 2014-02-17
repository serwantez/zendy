<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Css;

/**
 * Klasa bazowa edytowalnych zbiorów reprezentowanych przez zapytanie sql
 *
 * @author Piotr Zając
 */
class EditableQuery extends Query implements TableInterface {

    use EditableTrait,
        TableTrait;

    /**
     * Właściwości komponentu
     */

    const PROPERTY_TABLENAME = 'tableName';

    /*
     * Akcje na zbiorze
     */
    const ACTION_ADD = 'addAction';
    const ACTION_EDIT = 'editAction';
    const ACTION_CANCEL = 'cancelAction';
    const ACTION_ADDCOPY = 'addCopyAction';
    const ACTION_SAVE = 'saveAction';
    const ACTION_DELETE = 'deleteAction';
    const ACTION_TRUNCATE = 'truncateAction';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_MASTER,
        self::PROPERTY_NAME,
        self::PROPERTY_PRIMARY,
        self::PROPERTY_READONLY,
        self::PROPERTY_SELECT,
        self::PROPERTY_TABLENAME,
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->_table = new \Zend_Db_Table(array('name' => $this->_tableName));
        $this->_readOnly = false;
    }

    /**
     * Ustawia parametry akcji
     * 
     * @return \ZendY\Db\DataSet\EditableQuery
     */
    protected function _registerActions() {
        parent::_registerActions();

        $this->_registerAction(
                self::ACTION_ADD
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_PLUS)
                , 'Add'
                , NULL
                , false
                , Base::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_EDIT
                , self::ACTIONTYPE_STANDARD
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
     * Zwraca nazwę kolumny tabeli źródłowej na podstawie jej aliasu
     * 
     * @param string $alias
     * @return string
     */
    public function getTableField($alias) {
        $result = $alias;
        $selectCols = $this->_select->getPart(\Zend_Db_Select::COLUMNS);
        //pola z aliasami
        foreach ($selectCols as $column) {
            if ($column[2] == $alias) {
                $result = $column[1];
                break;
            }
        }
        return $result;
    }

}