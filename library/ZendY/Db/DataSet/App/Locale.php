<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\Table;
use ZendY\Db\Mysql;

/**
 * Zbiór języków
 *
 * @author Piotr Zając
 */
class Locale extends Table {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_CODE = 'code';
    const COL_NAME = 'name';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'locale';

    /**
     * Definicja struktury tabeli
     * 
     * @var array
     */
    static public $tableDefs = array(
        'tableName' => self::TABLE_NAME,
        'tableType' => Mysql::TABLE_TYPE_INNODB,
        'tableCharset' => Mysql::TABLE_CHARSET_UTF8,
        'fields' => array(
            array(
                'name' => self::COL_CODE,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 6,
                'null' => false
            ),
            array(
                'name' => self::COL_NAME,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 40,
                'null' => false
            )
        ),
        'primaryKey' => array(self::COL_CODE),
        'uniqueKey' => array(
            self::COL_NAME => array(self::COL_NAME)
        )
    );

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setTableName(self::TABLE_NAME);
        $this->setPrimary(self::COL_CODE);
    }

    /**
     * Zwraca rekordy startowe (domyślne)
     * 
     * @return array
     */
    static public function getStartRecords() {
        return array(
            array(
                self::COL_CODE => 'en_GB',
                self::COL_NAME => 'english'
            ),
            array(
                self::COL_CODE => 'pl_PL',
                self::COL_NAME => 'polski'
            )
        );
    }

}