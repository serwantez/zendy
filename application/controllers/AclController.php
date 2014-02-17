<?php

use Application\Form\User;
use Application\Form\Role;
use Application\Form\Rule;
use Application\Form\UserRole;

/**
 * Kontroler związany z listą kontroli dostępu (ACL)
 *
 * @author Piotr Zając
 */
class ACLController extends Zend_Controller_Action {

    public function roleAction() {
        $form = new Role(array(
                    Role::PROPERTY_NAME => 'roleForm',
                    Role::PROPERTY_AJAXVALIDATOR => false,
                ));
        $this->view->form = $form;
    }

    public function ruleAction() {
        $form = new Rule(array(
                    Rule::PROPERTY_NAME => 'ruleForm',
                    Rule::PROPERTY_AJAXVALIDATOR => false,
                ));
        $this->view->form = $form;
    }

    public function userAction() {
        $form = new User(array(
                    User::PROPERTY_NAME => 'userForm',
                    User::PROPERTY_AJAXVALIDATOR => false,
                ));
        $this->view->form = $form;
    }

    public function userroleAction() {
        $form = new UserRole(array(
                    UserRole::PROPERTY_NAME => 'userRoleForm',
                    UserRole::PROPERTY_AJAXVALIDATOR => false,
                ));
        $this->view->form = $form;
    }

}

