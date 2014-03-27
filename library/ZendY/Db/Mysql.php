<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

/**
 * Połączenie z bazą danych
 *
 * @author Piotr Zając
 */
class Mysql extends \ZendY\Object {
    /**
     * Wynik weryfikacji gotowości bazy danych 
     * do współpracy z biblioteką ZendY
     */

    const VERIFY_NO_CONNECT = 0;
    const VERIFY_NO_DATABASE = 1;
    const VERIFY_NO_TABLE = 2;
    const VERIFY_OK = 3;

    /**
     * Typy tabel
     */
    const TABLE_TYPE_MRGMYISAM = 'MRG_MYISAM';
    const TABLE_TYPE_MYISAM = 'MyISAM';
    const TABLE_TYPE_BLACKHOLE = 'BLACKHOLE';
    const TABLE_TYPE_CSV = 'CSV';
    const TABLE_TYPE_MEMORY = 'MEMORY';
    const TABLE_TYPE_ARCHIVE = 'ARCHIVE';
    const TABLE_TYPE_INNODB = 'InnoDB';

    /**
     * Metody porównywania napisów
     */
    const TABLE_CHARSET_CP1250 = 'cp1250';
    const TABLE_CHARSET_LATIN1 = 'latin1';
    const TABLE_CHARSET_LATIN2 = 'latin2';
    const TABLE_CHARSET_UTF16 = 'utf16';
    const TABLE_CHARSET_UTF32 = 'utf32';
    const TABLE_CHARSET_UTF8 = 'utf8';

    /**
     * Typy pól tabeli
     */
    const FIELD_TYPE_BIGINT = 'bigint';
    const FIELD_TYPE_DATE = 'date';
    const FIELD_TYPE_DECIMAL = 'decimal';
    const FIELD_TYPE_FLOAT = 'float';
    const FIELD_TYPE_INT = 'int';
    const FIELD_TYPE_LONGTEXT = 'longtext';
    const FIELD_TYPE_MEDIUMBLOB = 'mediumblob';
    const FIELD_TYPE_MEDIUMINT = 'mediumint';
    const FIELD_TYPE_MEDIUMTEXT = 'mediumtext';
    const FIELD_TYPE_SMALLINT = 'smallint';
    const FIELD_TYPE_TINYINT = 'tinyint';
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_TIMESTAMP = 'timestamp';
    const FIELD_TYPE_VARCHAR = 'varchar';

    static public $libTables = array(
        'ZendY\Db\DataSet\App\Locale',
        'ZendY\Db\DataSet\App\Role',
        'ZendY\Db\DataSet\App\Page',
        'ZendY\Db\DataSet\App\Rule',
        'ZendY\Db\DataSet\App\User',
        'ZendY\Db\DataSet\App\UserRole',
        'ZendY\Db\DataSet\App\Currency',
        'ZendY\Db\DataSet\App\Country',
    );

    /**
     * Sprawdza czy istnieje baza danych MySQL o podanych parametrach.
     * Jeśli nie istnieje, tworzy ją.
     * 
     * @param \Zend_Db_Adapter_Pdo_Mysql $db
     * @param bool $checkTables
     * @return integer
     */
    static protected function _verifyDatabase(\Zend_Db_Adapter_Pdo_Mysql $db, $checkTables = true) {
        $config = $db->getConfig();
        $dbname = addslashes($config['dbname']);

        $config['dbname'] = 'information_schema';
        $dbi = new \Zend_Db_Adapter_Pdo_Mysql($config);
        $q = $dbi->query("SELECT SCHEMA_NAME FROM SCHEMATA WHERE SCHEMA_NAME = ?"
                , array($dbname));
        if ($q->rowCount() == 0) {
            if ($dbi->getConnection()->exec(sprintf("CREATE DATABASE %s"
                                    , $dbname))) {
                $result = self::VERIFY_OK;
            } else
                $result = self::VERIFY_NO_DATABASE;
        } else {
            $result = self::VERIFY_OK;
        }
        if ($result == self::VERIFY_OK && $checkTables) {
            foreach (self::$libTables as $table) {
                $q = $dbi->query("SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?"
                        , array($dbname, $table::TABLE_NAME));
                if ($q->rowCount() == 0) {
                    if ($sql = $table::getCreateTable($table)) {
                        $db->getConnection()->exec($sql);
                    }
                    if ($sql = $table::getStartRecordsSQL($table)) {
                        $db->getConnection()->exec($sql);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Sprawdza, czy baza danych jest gotowa do współpracy z biblioteką ZendY
     * 
     * @param \Zend_Db_Adapter_Abstract $db
     * @return integer
     */
    static public function verify(\Zend_Db_Adapter_Abstract $db) {
        if ($db == null)
            return self::VERIFY_NO_DATABASE;
        try {
            if ($db instanceof \Zend_Db_Adapter_Pdo_Mysql) {
                $result = self::_verifyDatabase($db);
                if (!$result == self::VERIFY_OK) {
                    throw new Exception('Can\'t select database and create tables');
                }
            }
        } catch (Exception $e) {
            return self::VERIFY_NO_DATABASE;
        }
        return self::VERIFY_OK;
    }

    /**
     * Czy podany typ kolumny jest numeryczny
     * 
     * @param string $fieldType
     * @return bool
     */
    static public function isNumeric($fieldType) {
        return in_array($fieldType, array(
                    self::FIELD_TYPE_BIGINT,
                    self::FIELD_TYPE_FLOAT,
                    self::FIELD_TYPE_INT,
                    self::FIELD_TYPE_MEDIUMINT,
                    self::FIELD_TYPE_SMALLINT,
                    self::FIELD_TYPE_TINYINT,
                    self::FIELD_TYPE_DECIMAL
                ));
    }

}
