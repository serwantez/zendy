<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\Table;

/**
 * Zbiór słowników
 *
 * @author Piotr Zając
 */
class Lists extends Table {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_NAME = 'name';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'list';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this
                ->setTableName(self::TABLE_NAME)
                ->setPrimary(self::COL_ID)
        ;
    }

}
