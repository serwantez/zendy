<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Form;
use ZendY\Form\Element;
use ZendY\Form\Container;

class Auth extends Form {

    public function init() {
        //kontrolka tekstowa do podania nazwy użytkownika
        $login = new Element\IconEdit(array(
                    Element\IconEdit::PROPERTY_NAME => 'username',
                    Element\IconEdit::PROPERTY_LABEL => array(
                        'text' => 'User name',
                        'width' => 180
                    ),
                    Element\IconEdit::PROPERTY_ICON => Css::ICON_PERSON,
                    Element\IconEdit::PROPERTY_WIDTH => 180,
                    Element\IconEdit::PROPERTY_REQUIRED => true,
                ));

        //kontrolka hasła
        $password = new Element\IconPassword(array(
                    Element\IconPassword::PROPERTY_NAME => 'password',
                    Element\IconPassword::PROPERTY_LABEL => array(
                        'text' => 'Password',
                        'width' => 180
                    ),
                    Element\IconPassword::PROPERTY_WIDTH => 180,
                    Element\IconPassword::PROPERTY_REQUIRED => true,
                ));

        //przycisk logowania
        $submit = new Element\Submit(array(
                    Element\Submit::PROPERTY_NAME => 'login',
                    Element\Submit::PROPERTY_CAPTION => 'Log in',
                ));

        //przycisk rejestracji
        $signUp = new Element\Link(array(
                    Element\Link::PROPERTY_NAME => 'signup',
                    Element\Link::PROPERTY_HREF => '/auth/signup',
                    Element\Link::PROPERTY_TITLE => 'Click to sign up',
                    Element\Link::PROPERTY_TOOLTIP => true,
                    Element\Link::PROPERTY_VALUE => 'Sign up',
                ));

        //link do formularza przywracania hasła
        $recover = new Element\Link(array(
                    Element\Link::PROPERTY_NAME => 'recover',
                    Element\Link::PROPERTY_HREF => '/auth/recoverpassword',
                    Element\Link::PROPERTY_TITLE => 'Click to recover your password',
                    Element\Link::PROPERTY_TOOLTIP => true,
                    Element\Link::PROPERTY_VALUE => "I don't remember my password",
                ));

        //panel będący kontenerem dla przycisków i linku
        $btnPanel = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'btnPanel',
                    Container\Panel::PROPERTY_HEIGHT => array('value' => 3.2, 'unit' => 'em'),
                    Container\Panel::PROPERTY_CLASSES => array(
                        Css::SCROLL_DISABLE,
                    ),
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_BOTTOM,
                    Container\Panel::PROPERTY_SPACE => array('value' => 0.2, 'unit' => 'em'),
                ));
        $btnPanel->setElements(array($submit, $signUp, $recover));

        //panel z nagłówkiem będący kontenerem dla kontrolek nazwy użytkownika i hasła 
        //oraz dla panelu przycisków
        $mainBox = new Container\Box(array(
                    Container\Box::PROPERTY_NAME => 'mainBox',
                    Container\Box::PROPERTY_WIDTH => 440,
                    Container\Box::PROPERTY_HEIGHT => 180,
                    Container\Box::PROPERTY_TITLE => 'Logging',
                    Container\Box::PROPERTY_ALIGN => Css::ALIGN_CENTER,
                ));
        $mainBox->setElements(array($login, $password));
        $mainBox->setContainers(array($btnPanel));

        //dodanie głównego panelu do formularza
        $this->setContainers(array($mainBox));
    }

}