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
        $naviForm = new Page(array(
                    Page::PROPERTY_NAME => 'naviForm',
                    Page::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->naviForm = $naviForm;
    }

    public function countryAction() {
        $form = new Country(array(
                    Country::PROPERTY_NAME => 'countryForm',
                    Country::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->form = $form;
    }

    public function terytAction() {
        $form = new Teryt(array(
                    Teryt::PROPERTY_NAME => 'terytForm',
                    Teryt::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->form = $form->render();
    }

    public function entityAction() {
        $form = new Entity(array(
                    Entity::PROPERTY_NAME => 'entityForm',
                    Entity::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->form = $form;
    }

    public function listAction() {
        $form = new Lists(array(
                    Lists::PROPERTY_NAME => 'sortForm',
                    Lists::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->form = $form;
    }

    public function calendardayAction() {
        $form = new CalendarDay(array(
                    CalendarDay::PROPERTY_NAME => 'calendarDayForm',
                    CalendarDay::PROPERTY_AJAXVALIDATOR => false
                ));
        $this->view->form = $form;
    }

}

