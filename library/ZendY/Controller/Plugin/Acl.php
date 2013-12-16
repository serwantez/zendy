<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Controller\Plugin;

require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Plugin do kontroli uprawnień użytkowników
 *
 * @author Piotr Zając
 */
class Acl extends \Zend_Controller_Plugin_Abstract {

    /**
     * Obiekt kontroli dostępu
     * 
     * @var \Zend_Acl
     */
    protected $_acl = null;

    /**
     * Strona błędu
     * 
     * @var array
     */
    protected $_errorPage = array('module' => 'default', 'controller' => 'error', 'action' => 'error');

    /**
     * Strona logowania
     * 
     * @var array
     */
    protected $_authPage = array('module' => 'default', 'controller' => 'auth', 'action' => 'login');

    /**
     * Konstruktor
     * 
     * @param \Zend_Acl $acl
     * @param array $errorPage
     * @param array $authPage
     * @return void
     */
    public function __construct(\Zend_Acl $acl, $errorPage = null, $authPage = null) {
        $this->_acl = $acl;
        if (isset($errorPage))
            $this->_errorPage = $errorPage;
        if (isset($authPage))
            $this->_authPage = $authPage;
    }

    /**
     * Ustawia stronę błędu
     * 
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return \ZendY\Controller\Plugin\Acl
     */
    public function setErrorPage($module = 'default', $controller, $action) {
        $this->_errorPage = array('module' => $module, 'controller' => $controller, 'action' => $action);
        return $this;
    }

    /**
     * Ustawia stronę logowania
     * 
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return \ZendY\Controller\Plugin\Acl
     */
    public function setAuthPage($module = 'default', $controller, $action) {
        $this->_authPage = array('module' => $module, 'controller' => $controller, 'action' => $action);
        return $this;
    }

    /**
     * Kod wywoływany przed akcją, sprawdzający uprawnienia użytkownika
     * 
     * @param \Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
        $resource = $request->getControllerName();
        $action = $request->getActionName();

        //sprawdzenie prawa dostępu
        if (!$this->_acl->isAllowed(\Zend_Registry::get('role'), $resource, $action)) {
            if (\Zend_Registry::get('role') == 2) {
                $module = $this->_authPage['module'];
                $controller = $this->_authPage['controller'];
                $action = $this->_authPage['action'];
            } else {
                $module = $this->_errorPage['module'];
                $controller = $this->_errorPage['controller'];
                $action = $this->_errorPage['action'];
            }

            $request->setModuleName($module);
            $request->setControllerName($controller);
            $request->setActionName($action);
        }
    }

    /**
     * Zwraca obiekt kontroli dostępu
     * 
     * @return \Zend_Acl
     */
    public function getAcl() {
        return $this->_acl;
    }

}