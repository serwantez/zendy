<?php

namespace Application\Model;

use ZendY\Db\DataSet;

/**
 * Zbiór płci
 *
 * @author Piotr Zając
 */
class Sex extends DataSet\ArraySet {
    /**
     * Kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_NAME = 'name';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setData(array(
                    array(self::COL_ID => 1, self::COL_NAME => 'male'),
                    array(self::COL_ID => 2, self::COL_NAME => 'female')
                ))
                ->setPrimary(self::COL_ID);
    }

}

