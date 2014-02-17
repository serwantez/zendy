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
 * Zbiór uprawnień (reguł dla ACL)
 *
 * @author Piotr Zając
 */
class Rule extends EditableQuery {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_ROLE_ID = 'role_id';
    const COL_RULE_TYPE = 'rule_type';
    const COL_PAGE_ID = 'page_id';
    const COL_ASSERT = 'assert';
    const COL_ROLE_NAME = 'name';
    const COL_RESOURCE = 'resource';
    const COL_PRIVILEGE = 'privilege';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'rule';

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
                'type' => Mysql::FIELD_TYPE_INT,
                'length' => 11,
                'null' => false,
                'autoIncrement' => true
            ),
            array(
                'name' => self::COL_RULE_TYPE,
                'type' => Mysql::FIELD_TYPE_TINYINT,
                'length' => 1,
                'null' => false
            ),
            array(
                'name' => self::COL_ROLE_ID,
                'type' => Mysql::FIELD_TYPE_SMALLINT,
                'length' => 6,
                'null' => false
            ),
            array(
                'name' => self::COL_PAGE_ID,
                'type' => Mysql::FIELD_TYPE_INT,
                'length' => 11,
                'null' => true
            ),
            array(
                'name' => self::COL_ASSERT,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => true
            )
        ),
        'primaryKey' => array(self::COL_ID),
        'key' => array(
            'fk_rules_role1_idx' => array(self::COL_ROLE_ID),
            'fk_rules_page1_idx' => array(self::COL_PAGE_ID),
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
        $this->from(array('ru' => $this->_tableName), array(
                    self::COL_ID,
                    self::COL_ROLE_ID,
                    self::COL_RULE_TYPE,
                    self::COL_PAGE_ID,
                    self::COL_ASSERT
                ))
                ->joinLeft(array('ro' => Role::TABLE_NAME)
                        , "`ru`.`" . self::COL_ROLE_ID . "` = `ro`.`" . Role::COL_ID . "`"
                        , array(self::COL_ROLE_NAME, 'role_left' => Role::COL_LFT))
                ->joinLeft(array('p' => Page::TABLE_NAME)
                        , "`ru`.`" . self::COL_PAGE_ID . "` = `p`.`" . Page::COL_ID . "`"
                        , array(self::COL_RESOURCE, self::COL_PRIVILEGE, 'page_left' => Page::COL_LFT))
        /* ->order(array(
          "ro." . Role::COL_LFT,
          "p." . Page::COL_LFT,
          "ru." . self::COL_ID)) */
        ;
        $this->sortAction(array('field' => "role_left"), false);
        $this->sortAction(array('field' => "page_left"), false);
        $this->sortAction(array('field' => self::COL_ID), false);
        $this->setPrimary(self::COL_ID);
    }

    /**
     * Zwraca rekordy startowe (domyślne)
     * 
     * @return array
     */
    static public function getStartRecords() {
        return array(
            //wszyscy mają dostęp do strony głównej
            array(
                self::COL_ID => 1,
                self::COL_RULE_TYPE => 1,
                self::COL_ROLE_ID => 1,
                self::COL_PAGE_ID => 1,
                self::COL_ASSERT => ''
            ),
            //wszyscy mają dostęp do menu konta
            array(
                self::COL_ID => 2,
                self::COL_RULE_TYPE => 1,
                self::COL_ROLE_ID => 1,
                self::COL_PAGE_ID => 4,
                self::COL_ASSERT => ''
            ),
            //gość nie ma dostępu do wylogowywania
            array(
                self::COL_ID => 3,
                self::COL_RULE_TYPE => 0,
                self::COL_ROLE_ID => 2,
                self::COL_PAGE_ID => 9,
                self::COL_ASSERT => ''
            ),
            //zalogowany nie ma dostępu do ponownego logowania
            array(
                self::COL_ID => 4,
                self::COL_RULE_TYPE => 0,
                self::COL_ROLE_ID => 3,
                self::COL_PAGE_ID => 6,
                self::COL_ASSERT => ''
            ),
            //zalogowany nie może się zarejestrować
            array(
                self::COL_ID => 5,
                self::COL_RULE_TYPE => 0,
                self::COL_ROLE_ID => 3,
                self::COL_PAGE_ID => 5,
                self::COL_ASSERT => ''
            ),
            //gość nie ma dostępu do zmiany hasła
            array(
                self::COL_ID => 6,
                self::COL_RULE_TYPE => 0,
                self::COL_ROLE_ID => 2,
                self::COL_PAGE_ID => 7,
                self::COL_ASSERT => ''
            ),
            //zalogowany nie ma dostępu do przywracania hasła
            array(
                self::COL_ID => 7,
                self::COL_RULE_TYPE => 0,
                self::COL_ROLE_ID => 3,
                self::COL_PAGE_ID => 8,
                self::COL_ASSERT => ''
            ),
            //administrator ma dostęp do administrowania nawigacją strony
            array(
                self::COL_ID => 8,
                self::COL_RULE_TYPE => 1,
                self::COL_ROLE_ID => 4,
                self::COL_PAGE_ID => 2,
                self::COL_ASSERT => ''
            ),
            //administrator ma dostęp do administrowania użytkownikami
            array(
                self::COL_ID => 9,
                self::COL_RULE_TYPE => 1,
                self::COL_ROLE_ID => 4,
                self::COL_PAGE_ID => 10,
                self::COL_ASSERT => ''
            )
        );
    }

}