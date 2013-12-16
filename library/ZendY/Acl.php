<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

use ZendY\Db\DataSet\App;
use ZendY\Db\Filter;

/**
 * Kontrola dostępu użytkowników do zasobów strony
 * 
 * @author Piotr Zając
 */
class Acl extends \Zend_Acl {

    /**
     * Adapter bazodanowy
     * 
     * @var \Zend_Db_Adapter_Abstract 
     */
    private $_db;

    /**
     * Identyfikator wierzchołka nawigacji 
     * 
     * @var int
     */
    private $_pageId;

    /**
     * Identyfikator zalogowanego użytkownika
     * 
     * @var int 
     */
    protected $_user;

    /**
     * Konstruktor
     * 
     * @param int $pageId
     */
    public function __construct($pageId = 1) {
        $this->_db = \Zend_Registry::get('db');
        $this->_pageId = $pageId;
    }

    /**
     * Dodaje role użytkowników
     * 
     * @return \ZendY\Acl
     */
    public function addRoles() {
        $dataSet = new App\Role('role');
        $roles = $dataSet->getItems();
        for ($i = 0; $i < count($roles); $i++) {
            $this->addRole(new \Zend_Acl_Role($roles[$i][App\Role::COL_ID]), $roles[$i]['parent_id']);
        }

        if (\Zend_Auth::getInstance()->hasIdentity()) {
            //użytkownik zalogowany 
            $this->_user = \Zend_Auth::getInstance()->getStorage()->read()->id;

            $dataSet = new App\UserRole('userrole');
            $filter = new Filter();
            $filter->addFilter(App\UserRole::COL_USER_ID, $this->_user);
            $dataSet->filterAction(array('filter' => $filter));
            $parentRoles = null;
            if ($dataSet->getRecordCount()) {
                $parentRoles = $dataSet->fetchCol(App\UserRole::COL_ROLE_ID);
            }
            $this->addRole(new \Zend_Acl_Role('user' . $this->_user), $parentRoles);
            \Zend_Registry::set('role', 'user' . $this->_user);
        } else {
            //użytkownik niezalogowany
            \Zend_Registry::set('role', '2');
        }

        return $this;
    }

    /**
     * Dodaje zasoby
     * 
     * @return \ZendY\Acl
     */
    public function addResources() {
        $dataSet = new App\Page('page');
        $resources = $dataSet->getItems();
        $this->add(new \Zend_Acl_Resource('error'));
        $this->add(new \Zend_Acl_Resource('data'));
        for ($i = 0; $i < count($resources); $i++) {
            if ($resources[$i][App\Page::COL_RESOURCE]
                    && !$this->has($resources[$i][App\Page::COL_RESOURCE])) {
                $this->add(new \Zend_Acl_Resource($resources[$i][App\Page::COL_RESOURCE]));
            }
        }
        return $this;
    }

    /**
     * Dodaje uprawnienia rolom użytkowników
     * 
     * @return \ZendY\Acl
     */
    public function addRules() {
        $dataSet = new App\Rule('rule');
        $rules = $dataSet->getItems();
        $this->allow(null, 'error');
        $this->allow(null, 'data', array('validateform', 'data', 'image', 'upload'));
        $this->allow(null, 'auth', array('activateuser', 'newpassword'));
        for ($i = 0; $i < count($rules); $i++) {
            if ($rules[$i][App\Rule::COL_RESOURCE] == '')
                $rules[$i][App\Rule::COL_RESOURCE] = null;

            if ($rules[$i][App\Rule::COL_PRIVILEGE] == '')
                $rules[$i][App\Rule::COL_PRIVILEGE] = null;

            if (isset($rules[$i][App\Rule::COL_ASSERT])
                    && $rules[$i][App\Rule::COL_ASSERT] <> ''
                    && class_exists($rules[$i][App\Rule::COL_ASSERT])
                    && $rules[$i][App\Rule::COL_ASSERT] instanceof \Zend_Acl_Assert_Interface)
                $assert = new $rules[$i][App\Rule::COL_ASSERT];
            else
                $assert = null;

            if ($rules[$i][App\Rule::COL_RULE_TYPE] == 1) {
                $this->allow(
                        $rules[$i][App\Rule::COL_ROLE_ID]
                        , $rules[$i][App\Rule::COL_RESOURCE]
                        , $rules[$i][App\Rule::COL_PRIVILEGE]
                        , $assert);
            } else {
                $this->deny(
                        $rules[$i][App\Rule::COL_ROLE_ID]
                        , $rules[$i][App\Rule::COL_RESOURCE]
                        , $rules[$i][App\Rule::COL_PRIVILEGE]
                        , $assert);
            }
        }
        return $this;
    }

}