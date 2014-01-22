<?php

use Application\Form\Calendar;

/**
 * CalendarController
 *
 * Kontroler calendar
 *
 * @author Piotr Zając
 */
class CalendarController extends Zend_Controller_Action {

    public function indexAction() {
        $form = new Calendar();
        $this->view->form = $form;
    }

}

