<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\EditableQuery;
use ZendY\Db\DataSet\App\Role;
use ZendY\Db\DataSet\App\User;
use ZendY\Db\Mysql;

/**
 * Zbiór przypisań ról do użytkowników
 *
 * @author Piotr Zając
 */
class UserRole extends EditableQuery {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_USER_ID = 'user_id';
    const COL_USER_NAME = 'username';
    const COL_ROLE_ID = 'role_id';
    const COL_ROLE_NAME = 'role_name';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'user_role';

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
                'name' => self::COL_USER_ID,
                'type' => Mysql::FIELD_TYPE_INT,
                'length' => 11,
                'null' => false
            ),
            array(
                'name' => self::COL_ROLE_ID,
                'type' => Mysql::FIELD_TYPE_SMALLINT,
                'length' => 6,
                'null' => false
            )
        ),
        'primaryKey' => array(self::COL_ID)
    );

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->_name = self::TABLE_NAME;
        parent::init();
        $this->from(array('ur' => $this->_name), array(
                    self::COL_ID,
                    self::COL_ROLE_ID,
                    self::COL_USER_ID
                ))
                ->join(array('ro' => Role::TABLE_NAME)
                        , "`ur`.`" . self::COL_ROLE_ID . "` = `ro`.`" . Role::COL_ID . "`"
                        , array(self::COL_ROLE_NAME => Role::COL_NAME))
                ->join(array('u' => User::TABLE_NAME)
                        , "`ur`.`" . self::COL_USER_ID . "` = `u`.`" . User::COL_ID . "`"
                        , array(self::COL_USER_NAME))
        ;
        $this->setPrimary(self::COL_ID);
        $this->sortAction(array('field' => self::COL_USER_NAME));
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
                self::COL_USER_ID => 1,
                self::COL_ROLE_ID => 4
            )
        );
    }

    /**
     * Dodaje nową rolę użytkownikowi
     * 
     * @param array $data
     * @return \ZendY\Db\DataSet\App\UserRole
     */
    public function addUserRole($data) {
        $this->_table->createRow($data)->save();
        $this->_recordCount = $this->_count();
        return $this;
    }

}
