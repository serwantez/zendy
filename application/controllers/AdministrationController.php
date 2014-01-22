<?php

use Application\Form\Page;
use Application\Form\Country;
use Application\Form\Teryt;
use Application\Form\Entity;
use Application\Form\Lists;
use Application\Form\CalendarDay;

/**
 * Kontroler związany z prezentacją rejestrów danych
 *
 * @author Piotr Zając
 */
class AdministrationController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function navigationAction() {
        $naviForm = new Page();
        $this->view->naviForm = $naviForm;
    }

    public function countryAction() {
        $form = new Country();
        $this->view->form = $form;
    }

    public function terytAction() {
        $form = new Teryt();
        $url = $this->view->url(array('action' => 'teryt'));
        $form->setAction($url);
        $this->view->form = $form->render();
    }

    public function entityAction() {
        $form = new Entity();
        $url = $this->view->url(array('action' => 'entity'));
        $form->setAction($url);
        $this->view->form = $form;
    }

    public function listAction() {
        $form = new Lists();
        $this->view->form = $form;
    }

    public function calendardayAction() {
        $form = new CalendarDay();
        $this->view->form = $form;
    }

}

