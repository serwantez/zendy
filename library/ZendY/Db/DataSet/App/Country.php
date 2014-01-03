<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\EditableQuery;
use ZendY\Db\Mysql;

/**
 * Zbiór państw świata
 *
 * @author Piotr Zając
 */
class Country extends EditableQuery {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'country_id';
    const COL_NAME = 'name_en';
    const COL_NAME_PL = 'name_pl';
    const COL_ALFA2 = 'alfa2';
    const COL_ALFA3 = 'alfa3';
    const COL_DESCRIPTION = 'description';
    const COL_CURRENCY_ID = 'currency';
    const COL_FLAG = 'flag';
    const COL_POPULATION = 'population';
    const COL_UIC = 'uic';
    const COL_CURRENCY_NAME = 'currencyname';
    const COL_LINK_WIKI = 'link_wiki';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'country';

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
                'length' => 100,
                'null' => false
            ),
            array(
                'name' => self::COL_NAME_PL,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 100,
                'null' => false
            ),
            array(
                'name' => self::COL_ALFA2,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 2,
                'null' => true
            ),
            array(
                'name' => self::COL_ALFA3,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 3,
                'null' => true
            ),
            array(
                'name' => self::COL_DESCRIPTION,
                'type' => Mysql::FIELD_TYPE_LONGTEXT,
                'null' => true
            ),
            array(
                'name' => self::COL_CURRENCY_ID,
                'type' => Mysql::FIELD_TYPE_SMALLINT,
                'length' => 6,
                'null' => true
            ),
            array(
                'name' => self::COL_FLAG,
                'type' => Mysql::FIELD_TYPE_MEDIUMBLOB,
                'null' => true
            ),
            array(
                'name' => self::COL_LINK_WIKI,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 120,
                'null' => true
            )
        ),
        'primaryKey' => array(self::COL_ID),
        'uniqueKey' => array(
            self::COL_ALFA2 => array(self::COL_ALFA2),
            self::COL_ALFA3 => array(self::COL_ALFA3)
        ),
        'index' => array(
            self::COL_NAME => array(self::COL_NAME)
        )
    );

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->_name = self::TABLE_NAME;
        parent::init();
        $this->from(array('co' => $this->_name), array(
                    self::COL_ID,
                    self::COL_NAME,
                    self::COL_NAME_PL,
                    self::COL_ALFA2,
                    self::COL_ALFA3,
                    self::COL_DESCRIPTION,
                    self::COL_CURRENCY_ID,
                    self::COL_FLAG,
                    self::COL_POPULATION,
                    self::COL_UIC,
                    self::COL_LINK_WIKI
                ))
                ->joinLeft(array('cu' => Currency::TABLE_NAME)
                        , sprintf("co.%s = cu.%s", self::COL_CURRENCY_ID, Currency::COL_ID)
                        , array(
                    self::COL_CURRENCY_NAME => Currency::COL_NAME
                ))
        ;
        $this->setPrimary(self::COL_ID);
        $this->sortAction(array('field' => self::COL_NAME));
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
                self::COL_NAME => 'Poland',
                self::COL_NAME_PL => 'Polska',
                self::COL_ALFA2 => 'PL',
                self::COL_ALFA3 => 'POL',
                self::COL_DESCRIPTION => null,
                self::COL_CURRENCY_ID => 1,
                self::COL_FLAG => null,
                self::COL_LINK_WIKI => 'http://pl.wikipedia.org/wiki/Polska'
            )
        );
    }

}