<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Css;
use ZendY\Db\DataSet\EditableQuery;
use ZendY\Db\DataSet\App\Entity;
use ZendY\Db\DataSet\App\CalendarDay;

/**
 * Zbiór reprezentujący przypisanie obchodów kalendarzowych do jednostek, które je świętują
 *
 * @author Piotr Zając
 */
class EntityCalendar extends EditableQuery {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_ENTITY_ID = 'entity_id';
    const COL_ENTITY_NAME = 'entity_name_pl';
    const COL_CALENDAR_ID = 'calendar_id';
    const COL_CALENDAR_NAME = 'calendar_name_pl';
    const COL_DESCRIPTION = 'description';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'entity_calendar';

    /*
     * Akcje na zbiorze
     */
    const ACTION_ADDANDSAVE = 'addAndSaveAction';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setTableName(self::TABLE_NAME);
        $this->from(array('ec' => $this->_tableName), array(
                    self::COL_ID,
                    self::COL_ENTITY_ID,
                    self::COL_CALENDAR_ID,
                    self::COL_DESCRIPTION
                ))
                ->join(array('e' => Entity::TABLE_NAME)
                        , sprintf("ec.%s = e.%s"
                                , self::COL_ENTITY_ID
                                , Entity::COL_ID)
                        , array(
                    self::COL_ENTITY_NAME => Entity::COL_NAME
                ))
                ->join(array('c' => CalendarDay::TABLE_NAME)
                        , sprintf("ec.%s = c.%s"
                                , self::COL_CALENDAR_ID
                                , CalendarDay::COL_ID)
                        , array(
                    self::COL_CALENDAR_NAME => CalendarDay::COL_NAME
                ))
        ;
        $this->setPrimary(self::COL_ID);
    }

    /**
     * Ustawia parametry akcji
     * 
     * @return \ZendY\Db\DataSet\App\EntityCalendar
     */
    protected function _registerActions() {
        parent::_registerActions();

        $this->_registerAction(
                self::ACTION_ADDANDSAVE
                , self::ACTIONTYPE_SAVE
                , array('primary' => Css::ICON_PLUS)
                , 'Add'
                , NULL
                , TRUE
                , self::ACTION_PRIVILEGE_EDIT
        );
        return $this;
    }

    /**
     * Ustawia stan przycisków nawigacyjnych
     * 
     * @param array $params
     * @return \ZendY\Db\DataSet\App\EntityCalendar
     */
    protected function _setActionState($params = array()) {
        parent::_setActionState($params);

        $this->_navigator[self::ACTION_ADDANDSAVE] = (
                ($this->_state == self::STATE_VIEW || $this->_state == self::STATE_EDIT)
                && !$this->_readOnly);
        return $this;
    }

    /**
     * Dodaje i zapisuje nowy podmiot
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function addAndSaveAction($params = array(), $compositePart = false) {
        $this->_state = self::STATE_INSERT;
        $result = parent::saveAction($params, $compositePart);
        return $result;
    }

}