<?php

use Application\Form\Manual;
use Application\Form\Classpreview;

/**
 * ZendyController
 *
 * Kontroler dokumentacji biblioteki ZendY
 *
 * @author Piotr Zając
 */
class ZendyController extends Zend_Controller_Action {

    public function manualAction() {
        $this->view->headLink()->appendStylesheet('/library/components/svg/svg.css');
        $form = new Manual();
        $this->view->form = $form;
    }

    public function classpreviewAction() {
        $request = \Zend_Controller_Front::getInstance()->getRequest();
        $id = $request->getParam('id');
        $form = new Classpreview(array('search' => $id));
        $this->view->form = $form;
    }

    public function downloadAction() {
        //$this->view->headLink()->appendStylesheet('/library/components/form/svg.css');
    }

}

