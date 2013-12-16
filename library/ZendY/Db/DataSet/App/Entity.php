<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\NestedTree;

/**
 * Zbiór jednostek organizacyjnych
 *
 * @author Piotr Zając
 */
class Entity extends NestedTree {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_NAME = 'name_pl';
    const COL_TYPE = 'type';
    const COL_ADDRESS = 'address';
    const COL_POSTAL_CODE = 'postal_code';
    const COL_POSTAL_LOCALITY = 'postal_locality';
    const COL_COUNTRY_ID = 'country_id';
    const COL_TERYT = 'teryt';
    const COL_PHONE = 'phone';
    const COL_FAX = 'fax';
    const COL_EMAIL = 'email';
    const COL_PHOTO = 'photo';
    const COL_WEBSITE = 'website';
    const COL_COORDINATES = 'coordinates';
    const COL_CODE = 'code';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'entity';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setTableName(self::TABLE_NAME);
        $this->setPrimary(self::COL_ID);
    }

}