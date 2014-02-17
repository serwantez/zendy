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
        $form = new Worker(array(
                    Worker::PROPERTY_NAME => 'workerForm',
                    Worker::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->form = $form;
    }

    public function airportAction() {
        $form = new Airport(array(
                    Airport::PROPERTY_NAME => 'airportForm',
                    Airport::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->form = $form;
    }

    public function terytAction() {
        $form = new Teryt(array(
                    Teryt::PROPERTY_NAME => 'terytForm',
                    Teryt::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->form = $form;
    }

}

