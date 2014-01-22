<?php

use Application\Form\Worker;
use Application\Form\Airport;
use Application\Form\Teryt;

/**
 * DemoController
 *
 * Kontroler demo
 *
 * @author Piotr Zając
 */
class DemoController extends Zend_Controller_Action {

    public function workerAction() {
        $form = new Worker();
        $this->view->form = $form;
    }

    public function airportAction() {
        $form = new Airport();
        $this->view->form = $form;
    }

    public function terytAction() {
        $form = new Teryt();
        $this->view->form = $form;
    }

}

