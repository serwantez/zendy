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
 * Zbiór użytkowników
 *
 * @author Piotr Zając
 */
class User extends Table {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_USERNAME = 'username';
    const COL_PASSWORD = 'password';
    const COL_ACTIVE = 'active';
    const COL_FIRSTNAME = 'firstname';
    const COL_SURNAME = 'surname';
    const COL_SEX = 'sex';
    const COL_EMAIL = 'email';
    const COL_ADDITION_TIME = 'addition_time';
    const COL_PHOTO = 'photo';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'user';

    /**
     * Funkcje szyfrujące
     */
    const CF_MD5 = 'MD5';
    const CF_SHA1 = 'SHA1';

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
                'name' => self::COL_USERNAME,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => false
            ),
            array(
                'name' => self::COL_PASSWORD,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 64,
                'null' => false
            ),
            array(
                'name' => self::COL_ACTIVE,
                'type' => Mysql::FIELD_TYPE_TINYINT,
                'length' => 1,
                'null' => false,
                'default' => 0
            ),
            array(
                'name' => self::COL_FIRSTNAME,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => false
            ),
            array(
                'name' => self::COL_SURNAME,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => false
            ),
            array(
                'name' => self::COL_SEX,
                'type' => Mysql::FIELD_TYPE_TINYINT,
                'length' => 1,
                'null' => false,
                'default' => 0
            ),            
            array(
                'name' => self::COL_EMAIL,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 70,
                'null' => false
            ),
            array(
                'name' => self::COL_ADDITION_TIME,
                'type' => Mysql::FIELD_TYPE_TIMESTAMP,
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ),
            array(
                'name' => self::COL_PHOTO,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 255,
                'null' => true
            )
        ),
        'primaryKey' => array(self::COL_ID),
        'uniqueKey' => array(
            self::COL_USERNAME => array(self::COL_USERNAME)
        )
    );

    /**
     * Dodatkowy kod zabezpieczający szyfrowane hasło
     * 
     * @var string 
     */
    protected static $_passString = "!@#qwe";

    /**
     * Nazwa algorytmu szyfrującego
     * 
     * @var string
     */
    protected static $_cryptographicFunction = self::CF_MD5;

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
        $call = self::$_cryptographicFunction;
        return array(
            array(
                self::COL_ID => 1,
                self::COL_USERNAME => 'admin',
                self::COL_PASSWORD => $call('admin' . self::$_passString),
                self::COL_ACTIVE => 1,
                self::COL_FIRSTNAME => 'Administrator',
                self::COL_SURNAME => '',
                self::COL_EMAIL => '',
                self::COL_ADDITION_TIME => null,
                self::COL_PHOTO => null
            )
        );
    }

    /**
     * Ustawia nazwę algorytmu szyfrującego
     * 
     * @param string $cryptopgraphicFunction
     * @return void
     */
    public static function setCryptographicFunction($cryptopgraphicFunction) {
        self::$_cryptographicFunction = $cryptopgraphicFunction;
    }

    /**
     * Zwraca nazwę algorytmu szyfrującego
     * 
     * @return string
     */
    public static function getCryptographicFunction() {
        return self::$_cryptographicFunction;
    }

    /**
     * Ustawia dodatkowy kod zabezpieczający szyfrowane hasło
     * 
     * @param string $passString
     * @return void
     */
    public static function setPassString($passString) {
        self::$_passString = $passString;
    }

    /**
     * Zwraca dodatkowy kod zabezpieczający szyfrowane hasło
     * 
     * @return string
     */
    public static function getPassString() {
        return self::$_passString;
    }

    /**
     * Tworzy i zwraca bazodanowy adapter autoryzacyjny
     * 
     * @return \Zend_Auth_Adapter_DbTable
     */
    public static function createAuthAdapter() {
        return new \Zend_Auth_Adapter_DbTable(
                        \Zend_Registry::get('db'),
                        'user',
                        self::COL_USERNAME,
                        self::COL_PASSWORD,
                        sprintf("%s(CONCAT(?, '%s')) AND %s = 1"
                                , self::$_cryptographicFunction
                                , self::$_passString
                                , self::COL_ACTIVE
                        )
        );
    }

    /**
     * Dodaje nowego użytkownika
     * 
     * @param array $data
     * @return int user id
     */
    public function addUser(array $data) {
        $data[self::COL_PASSWORD] = new \Zend_Db_Expr(self::$_cryptographicFunction . "('" . $data[self::COL_PASSWORD] . self::$_passString . "')");
        $userId = $this->_table->createRow($data)->save();
        $this->_recordCount = $this->_count();
        return $userId;
    }

    /**
     * Dokonuje aktywacji użytkownika
     * 
     * @param string $hashedLogin
     * @return bool
     */
    public function activateUser($hashedLogin) {
        $sql = $this->_table->select()->where(self::$_cryptographicFunction . '(' . self::COL_USERNAME . ') = ?', $hashedLogin);
        $id = $this->_db->fetchOne($sql);

        if ($id) {
            $this->_table->update(array(self::COL_ACTIVE => 1), array(self::COL_ID . ' = ?' => $id));
            return true;
        } else {
            return false;
        }
    }

    /**
     * Zmienia hasło użytkownika o podanym id
     * 
     * @param string $newPassword
     * @param int $id
     * @return \ZendY\Db\DataSet\App\User
     */
    public function changePassword($newPassword, $id) {
        $data = array(
            self::COL_PASSWORD => new \Zend_Db_Expr(self::$_cryptographicFunction . "('" . $newPassword . self::$_passString . "')")
        );
        $this->_table->update($data, array(self::COL_ID . ' = ?' => $id));
        return $this;
    }

    /**
     * Zmienia hasło użytkownika o podanym zaszyfrowanym loginie
     * 
     * @param string $newPassword
     * @param string $hashedLogin
     * @return \ZendY\Db\DataSet\App\User
     */
    public function newPassword($newPassword, $hashedLogin) {
        $sql = $this->_table->select()
                ->where(self::$_cryptographicFunction . '(' . self::COL_USERNAME . ') = ?', $hashedLogin);
        $id = $this->_db->fetchOne($sql);

        if ($id) {
            $data = array(
                self::COL_PASSWORD => new \Zend_Db_Expr(self::$_cryptographicFunction . "('" . $newPassword . self::$_passString . "')")
            );
            $this->_table->update($data, array(self::COL_ID . ' = ?' => $id));
            return TRUE;
        } else {
            return FALSE;
        }
    }

}