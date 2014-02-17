<?php

namespace Application\Model;

use ZendY\Db\DataSet;

/**
 * Zbiór portów lotniczych
 *
 * @author Piotr Zając
 */
class Airport extends DataSet\EditableQuery {
    /**
     * Kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_NAME = 'airport_name';
    const COL_CODE = 'code';
    const COL_COORDINATES = 'coordinates';
    const COL_COUNTRY_ID = 'country_id';
    const COL_COUNTRY_NAME = 'country_name';

    /**
     * Nazwa tabeli
     */
    const TABLE_NAME = 'airport';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setTableName(self::TABLE_NAME);
        $this->setPrimary(self::COL_ID);
        $this->from(array('a' => $this->_tableName), array(
                    self::COL_ID,
                    self::COL_NAME,
                    self::COL_CODE,
                    self::COL_COORDINATES,
                    self::COL_COUNTRY_ID
                ))
                ->joinLeft(array('co' => DataSet\App\Country::TABLE_NAME)
                        , sprintf("a.%s = co.%s", self::COL_COUNTRY_ID, DataSet\App\Country::COL_ID)
                        , array(
                    self::COL_COUNTRY_NAME => DataSet\App\Country::COL_NAME
                ))
        ;
        $this->sortAction(array('field' => self::COL_NAME));
    }

}

