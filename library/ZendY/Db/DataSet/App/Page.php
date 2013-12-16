<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Css;
use ZendY\Db\DataSet\NestedTree;
use ZendY\Db\Mysql;

/**
 * Zbiór do zarządzania nawigacją strony
 *
 * @author Piotr Zając
 */
class Page extends NestedTree {
    /**
     * Akcje
     */

    const ACTION_CREATEPAGE = 'createPageAction';

    /**
     * Domyślne kolumny zbioru
     */
    const COL_ID = 'id';
    const COL_LABEL = 'label';
    const COL_URI = 'uri';
    const COL_RESOURCE = 'resource';
    const COL_PRIVILEGE = 'privilege';
    const COL_CLASS = 'class';
    const COL_VISIBLE = 'visible';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'page';

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
                'name' => self::COL_LABEL,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => false
            ),
            array(
                'name' => self::COL_URI,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 150,
                'null' => false,
                'default' => '#'
            ),
            array(
                'name' => self::COL_RESOURCE,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => true
            ),
            array(
                'name' => self::COL_PRIVILEGE,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => true
            ),
            array(
                'name' => self::COL_CLASS,
                'type' => Mysql::FIELD_TYPE_VARCHAR,
                'length' => 50,
                'null' => true
            ),
            array(
                'name' => self::COL_VISIBLE,
                'type' => Mysql::FIELD_TYPE_TINYINT,
                'length' => 1,
                'null' => false,
                'default' => 1
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
            )
        ),
        'primaryKey' => array(self::COL_ID),
        'key' => array(
            self::COL_LFT => array(self::COL_LFT)
        )
    );

    /**
     * Nazwa pola przechowującego adres strony
     * 
     * @var string
     */
    protected $_uriField = self::COL_URI;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->setTableName(self::TABLE_NAME);
        $this->setPrimary(self::COL_ID);
        $this->_childrenField = 'pages';
    }

    /**
     * Ustawia parametry akcji
     * 
     * @return \ZendY\Db\DataSet\App\Page
     */
    protected function _registerActions() {
        parent::_registerActions();
        $this->_registerAction(
                self::ACTION_CREATEPAGE
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_DOCUMENT)
                , 'Create page'
                , null
                , false
                , self::ACTION_PRIVILEGE_EDIT
        );

        return $this;
    }

    /**
     * Ustawia pole przechowujące adres strony
     * 
     * @param string $field
     * @return \ZendY\Db\DataSet\App\Page
     */
    public function setUriField($field) {
        $this->_uriField = (string) $field;
        return $this;
    }

    /**
     * Zwraca pole przechowujące adres strony
     * 
     * @return string
     */
    public function getUriField() {
        return $this->_uriField;
    }

    /**
     * Akcja tworząca kontroler i akcję zawartą w podanym adresie uri
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function createPageAction($params = array(), $compositePart = false) {
        $result = array();
        $cur = $this->getCurrent();
        if (array_key_exists($this->_uriField, $cur)) {
            $uri = $cur[$this->_uriField];
            //wyłuskanie nazwy kontrolera i akcji
            $request = new \Zend_Controller_Request_Http();
            $request->setRequestUri($uri);
            \Zend_Controller_Front::getInstance()->getRouter()->route($request);
            $controller = $request->getControllerName();
            $action = $request->getActionName();

            if (isset($controller) && $controller <> '' && $controller <> '#') {
                $cg = new \ZendY\Code\Generator\Php\Zend\Controller($controller);
                //$cg->addMethod('init');
                if (isset($action) && $action <> '') {
                    $actionBody = sprintf('$form = new %s();' . "\n" . '$this->view->form = $form;', ucfirst($action));
                    $viewBody = 'echo $this->form->render();';
                    $cg->addAction($action, array(), TRUE, $actionBody, null, $viewBody);
                }
                $cg->write();
            }
        } else {
            $result[] = 'Unknown uri field';
        }

        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Ustawia stan przycisków nawigacyjnych
     * 
     * @param array $params
     * @return \ZendY\Db\DataSet\App\Page
     */
    protected function _setActionState($params = array()) {
        parent::_setActionState($params);
        $cur = $this->getCurrent();
        $this->_navigator[self::ACTION_CREATEPAGE] = ($this->_state == self::STATE_VIEW || $this->_state == self::STATE_EDIT);
        return $this;
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
                self::COL_LABEL => 'Root',
                self::COL_URI => '/',
                self::COL_RESOURCE => 'index',
                self::COL_PRIVILEGE => 'index',
                self::COL_CLASS => 'ui-icon-home',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 1,
                self::COL_RGT => 28
            ),
            array(
                self::COL_ID => 2,
                self::COL_LABEL => 'Administration',
                self::COL_URI => '#',
                self::COL_RESOURCE => 'administration',
                self::COL_PRIVILEGE => '',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 2,
                self::COL_RGT => 15
            ),
            array(
                self::COL_ID => 3,
                self::COL_LABEL => 'Navigation',
                self::COL_URI => '/administration/navigation',
                self::COL_RESOURCE => 'administration',
                self::COL_PRIVILEGE => 'navigation',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 3,
                self::COL_RGT => 4
            ),
            array(
                self::COL_ID => 10,
                self::COL_LABEL => 'ACL',
                self::COL_URI => '#',
                self::COL_RESOURCE => 'acl',
                self::COL_PRIVILEGE => '',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 5,
                self::COL_RGT => 14
            ),
            array(
                self::COL_ID => 11,
                self::COL_LABEL => 'Users',
                self::COL_URI => '/acl/user',
                self::COL_RESOURCE => 'acl',
                self::COL_PRIVILEGE => 'user',
                self::COL_CLASS => 'ui-icon-person',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 6,
                self::COL_RGT => 7
            ),
            array(
                self::COL_ID => 12,
                self::COL_LABEL => 'Roles',
                self::COL_URI => '/acl/role',
                self::COL_RESOURCE => 'acl',
                self::COL_PRIVILEGE => 'role',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 8,
                self::COL_RGT => 9
            ),
            array(
                self::COL_ID => 13,
                self::COL_LABEL => 'Rules',
                self::COL_URI => '/acl/rule',
                self::COL_RESOURCE => 'acl',
                self::COL_PRIVILEGE => 'rule',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 10,
                self::COL_RGT => 11
            ),
            array(
                self::COL_ID => 14,
                self::COL_LABEL => 'User roles',
                self::COL_URI => '/acl/userrole',
                self::COL_RESOURCE => 'acl',
                self::COL_PRIVILEGE => 'userrole',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 12,
                self::COL_RGT => 13
            ),
            array(
                self::COL_ID => 4,
                self::COL_LABEL => 'Account',
                self::COL_URI => '#',
                self::COL_RESOURCE => 'auth',
                self::COL_PRIVILEGE => '',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 16,
                self::COL_RGT => 27
            ),
            array(
                self::COL_ID => 5,
                self::COL_LABEL => 'Sign up',
                self::COL_URI => '/auth/signup',
                self::COL_RESOURCE => 'auth',
                self::COL_PRIVILEGE => 'signup',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 17,
                self::COL_RGT => 18
            ),
            array(
                self::COL_ID => 6,
                self::COL_LABEL => 'Log in',
                self::COL_URI => '/auth/login',
                self::COL_RESOURCE => 'auth',
                self::COL_PRIVILEGE => 'login',
                self::COL_CLASS => 'ui-icon-key',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 19,
                self::COL_RGT => 20
            ),
            array(
                self::COL_ID => 7,
                self::COL_LABEL => 'Change password',
                self::COL_URI => '/auth/changepassword',
                self::COL_RESOURCE => 'auth',
                self::COL_PRIVILEGE => 'changepassword',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 21,
                self::COL_RGT => 22
            ),
            array(
                self::COL_ID => 8,
                self::COL_LABEL => 'Recover password',
                self::COL_URI => '/auth/recoverpassword',
                self::COL_RESOURCE => 'auth',
                self::COL_PRIVILEGE => 'recoverpassword',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 23,
                self::COL_RGT => 24
            ),
            array(
                self::COL_ID => 9,
                self::COL_LABEL => 'Log out',
                self::COL_URI => '/auth/logout',
                self::COL_RESOURCE => 'auth',
                self::COL_PRIVILEGE => 'logout',
                self::COL_CLASS => '',
                self::COL_VISIBLE => 1,
                self::COL_LFT => 25,
                self::COL_RGT => 26
            )
        );
    }

}