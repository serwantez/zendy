<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\EditableQuery;

/**
 * Zbiór obchodów kalendarzowych
 *
 * @author Piotr Zając
 */
class CalendarDay extends EditableQuery {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_MOVABILITY = 'movability';
    const COL_MOVABILITY_NAME = 'movability_name';
    const COL_NAME = 'name_pl';
    const COL_DAY = 'day';
    const COL_WEIGHT_TYPE = 'weight_type';
    const COL_WEIGHT_TYPE_NAME = 'weight_type_name';
    const COL_WEIGHT_NUMBER = 'weight_number';
    const COL_DEPENDENCY_FUNCTION = 'dependency_function';
    const COL_DEPENDENCY_FUNCTION_NAME = 'dependency_function_name';
    const COL_DEPENDENCY_PARAM = 'dependency_param';
    const COL_HOLIDAY = 'holiday';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'calendar';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setTableName(self::TABLE_NAME);
        $this->from(array('c' => $this->_tableName), array(
                    self::COL_ID,
                    self::COL_MOVABILITY,
                    self::COL_NAME,
                    self::COL_DAY,
                    self::COL_WEIGHT_TYPE,
                    self::COL_WEIGHT_NUMBER,
                    self::COL_DEPENDENCY_FUNCTION,
                    self::COL_DEPENDENCY_PARAM,
                    self::COL_HOLIDAY
                ))
                ->join(array('wt' => ListItem::TABLE_NAME)
                        , sprintf("`c`.`%s` = `wt`.`%s` and `wt`.`%s` = 10"
                                , self::COL_WEIGHT_TYPE
                                , ListItem::COL_ITEM_ID
                                , ListItem::COL_LIST_ID)
                        , array(
                    self::COL_WEIGHT_TYPE_NAME => Lists::COL_NAME
                ))
                ->join(array('m' => ListItem::TABLE_NAME)
                        , sprintf("`c`.`%s` = `m`.`%s` and `m`.`%s` = 11"
                                , self::COL_MOVABILITY
                                , ListItem::COL_ITEM_ID
                                , ListItem::COL_LIST_ID)
                        , array(
                    self::COL_MOVABILITY_NAME => ListItem::COL_NAME
                ))
                ->joinLeft(array('df' => ListItem::TABLE_NAME)
                        , sprintf("`c`.`%s` = `df`.`%s` and `df`.`%s` = 12"
                                , self::COL_DEPENDENCY_FUNCTION
                                , ListItem::COL_ITEM_ID
                                , ListItem::COL_LIST_ID)
                        , array(
                    self::COL_DEPENDENCY_FUNCTION_NAME => ListItem::COL_NAME
                ))
        ;
        $this->setPrimary(self::COL_ID);
    }

}