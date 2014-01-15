<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\NestedTree;
use ZendY\Db\Mysql;

/**
 * Zbiór ról użytkowników
 *
 * @author Piotr Zając
 */
class Role extends NestedTree {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_NAME = 'name';
    const COL_CLASS = 'class';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'role';

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
                'null' => false,
                'autoIncrement' => true
            ),
            array(
                'name' => self::COL_NAME,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => false
            ),
            array(
                'name' => self::COL_CLASS,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => true
            ),
            array(
                'name' => self::COL_LFT,
                'type' => Mysql::FIELD_TYPE_INT,
                'length' => 11,
                'null' => false
            ),
            array(
                'name' => self::COL_RGT,
                'type' => Mysql::FIELD_TYPE_INT,
                'length' => 11,
                'null' => false
            ),
            array(
                'name' => self::COL_PARENT,
                'type' => Mysql::FIELD_TYPE_INT,
                'length' => 11,
                'null' => null
            )
        ),
        'primaryKey' => array(self::COL_ID),
        'key' => array(
            self::COL_LFT => array(self::COL_LFT)
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
                self::COL_NAME => 'Everybody',
                self::COL_CLASS => '',
                self::COL_LFT => 1,
                self::COL_RGT => 8,
                self::COL_PARENT => null
            ),
            array(
                self::COL_ID => 2,
                self::COL_NAME => 'Guest',
                self::COL_CLASS => '',
                self::COL_LFT => 2,
                self::COL_RGT => 3,
                self::COL_PARENT => 1
            ),
            array(
                self::COL_ID => 3,
                self::COL_NAME => 'Member',
                self::COL_CLASS => '',
                self::COL_LFT => 4,
                self::COL_RGT => 7,
                self::COL_PARENT => 1
            ),
            array(
                self::COL_ID => 4,
                self::COL_NAME => 'Administrator',
                self::COL_CLASS => '',
                self::COL_LFT => 5,
                self::COL_RGT => 6,
                self::COL_PARENT => 3
            )
        );
    }

}