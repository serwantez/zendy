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
        $form = new Role();
        $this->view->form = $form;
    }

    public function ruleAction() {
        $form = new Rule();
        $this->view->form = $form;
    }

    public function userAction() {
        $form = new User();
        $this->view->form = $form;
    }

    public function userroleAction() {
        $form = new UserRole();
        $this->view->form = $form;
    }

}

