<?php

class ImportController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function zusAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $http = new ZendY_Http_ZUS();
        $this->view->http = $http;
    }

}

