<?php

namespace Application\Model;

use ZendY\Db\DataSet;

/**
 * Zbiór pracowników
 *
 * @author Piotr Zając
 */
class Worker extends DataSet\EditableQuery {
    /**
     * Kolumny zbioru
     */

    const COL_ID = 'worker_id';
    const COL_FIRSTNAME = 'firstname';
    const COL_SURNAME = 'surname';
    const COL_COUNTRY_ID = 'country_id';
    const COL_ADDRESS = 'address';
    const COL_POSTAL_CODE = 'postal_code';
    const COL_POST = 'post';
    const COL_PHONE = 'phone';
    const COL_EMAIL = 'email';
    const COL_SEX = 'sex';
    const COL_PHOTO = 'photo';
    const COL_COUNTRY_NAME = 'country_name';

    /**
     * Nazwa tabeli
     */
    const TABLE_NAME = 'worker';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->_name = self::TABLE_NAME;
        parent::init();
        $this->setPrimary(self::COL_ID);
        $this->from(array('wo' => $this->_name), array(
                    self::COL_ID,
                    self::COL_FIRSTNAME,
                    self::COL_SURNAME,
                    self::COL_COUNTRY_ID,
                    self::COL_ADDRESS,
                    self::COL_POSTAL_CODE,
                    self::COL_POST,
                    self::COL_PHONE,
                    self::COL_EMAIL,
                    self::COL_SEX,
                    self::COL_PHOTO
                ))
                ->join(array('co' => DataSet\App\Country::TABLE_NAME)
                        , sprintf("wo.%s = co.%s", self::COL_COUNTRY_ID, DataSet\App\Country::COL_ID)
                        , array(
                    self::COL_COUNTRY_NAME => DataSet\App\Country::COL_NAME
                ))
        ;
        $this->sortAction(array('field' => self::COL_SURNAME));
    }

}

