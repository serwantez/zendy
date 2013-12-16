<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\NestedTree;

/**
 * Zbiór dokumentów elektronicznych
 *
 * @author Piotr Zając
 */
class Doc extends NestedTree {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_NAME = 'name';
    const COL_FULLNAME = 'fullname';
    const COL_FILENAME = 'filename';
    const COL_CREATION_USER = 'creation_user';
    const COL_CREATION_USERNAME = 'creation_username';
    const COL_CREATION_DATE = 'creation_date';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'doc';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setTableName(self::TABLE_NAME);
        $this->setPrimary(self::COL_ID);
        $this->sortAction(array('field' => self::COL_NAME));
    }

    /**
     * Zapisuje zmiany w bieżącym wierszu
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function saveAction($params = array(), $compositePart = false) {
        //pobranie id zalogowanego użytkownika
        $auth = \Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity()->id;
            $params['fieldsValues'][self::COL_CREATION_USER] = $user;
        }
        unset($params['fieldsValues'][self::COL_CREATION_DATE]);
        return parent::saveAction($params, $compositePart);
    }

}