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
 * Zbiór jednostek monetarnych świata
 *
 * @author Piotr Zając
 */
class Currency extends Table {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_NAME = 'name';
    const COL_ISO = 'iso';
    const COL_CODE = 'code';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'currency';

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
                'name' => self::COL_ID,
                'type' => Mysql::FIELD_TYPE_SMALLINT,
                'length' => 6,
                'null' => false
            ),
            array(
                'name' => self::COL_NAME,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 80,
                'null' => false
            ),
            array(
                'name' => self::COL_ISO,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 3,
                'null' => false
            ),
            array(
                'name' => self::COL_CODE,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 6,
                'null' => true
            )
        ),
        'primaryKey' => array(self::COL_ID),
        'uniqueKey' => array(
            self::COL_ISO => array(self::COL_ISO)
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
        $this->setPrimary(self::COL_ID);
    }
    
    /**
     * Zwraca rekordy startowe (domyślne)
     * 
     * @return array
     */
    static public function getStartRecords() {
        return array(
            array(
                self::COL_ID => 1,
                self::COL_NAME => 'Zloty',
                self::COL_ISO => 'PLN',
                self::COL_CODE => 985
            )
        );
    }
    

}