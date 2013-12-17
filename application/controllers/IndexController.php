<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $this->view->headLink()
                ->appendStylesheet($this->view->host . '/library/components/svg/svg.css')
                ->appendStylesheet($this->view->host . '/library/components/text/text.css')
        ;
    }

}

