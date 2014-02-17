<?php

namespace Application\Model;

use ZendY\Db\DataSet;

/**
 * Zbiór umów z pracownikami
 *
 * @author Piotr Zając
 */
class Agreement extends DataSet\EditableQuery {
    /**
     * Kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_WORKER_ID = 'worker_id';
    const COL_AGREEMENT_TYPE_ID = 'agreement_type_id';
    const COL_JOB_TIME = 'job_time';
    const COL_DATE_SIGNING = 'date_signing';
    const COL_DATE_START = 'date_start';
    const COL_DATE_END = 'date_end';
    const COL_WORKER_NAME = 'worker_name';
    const COL_AGREEMENT_TYPE_NAME = 'agreement_type_name';

    /**
     * Nazwa tabeli
     */
    const TABLE_NAME = 'agreement';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setTableName(self::TABLE_NAME);
        $this->setPrimary(self::COL_ID);
        $this->from(array('ag' => $this->_tableName), array(
                    self::COL_ID,
                    self::COL_WORKER_ID,
                    self::COL_AGREEMENT_TYPE_ID,
                    self::COL_JOB_TIME,
                    self::COL_DATE_SIGNING,
                    self::COL_DATE_START,
                    self::COL_DATE_END
                ))
                ->join(array('wo' => Worker::TABLE_NAME)
                        , sprintf("ag.%s = wo.%s", self::COL_WORKER_ID, Worker::COL_ID)
                        , array(
                    self::COL_WORKER_NAME => Worker::COL_SURNAME
                ))
                ->join(array('at' => DataSet\App\ListItem::TABLE_NAME)
                        , sprintf("ag.%s = at.%s and at.%s = 16"
                                , self::COL_AGREEMENT_TYPE_ID
                                , DataSet\App\ListItem::COL_ITEM_ID
                                , DataSet\App\ListItem::COL_LIST_ID)
                        , array(
                    self::COL_AGREEMENT_TYPE_NAME => DataSet\App\ListItem::COL_NAME
                ))
        ;
        $this->sortAction(array('field' => self::COL_DATE_SIGNING));
    }

}

