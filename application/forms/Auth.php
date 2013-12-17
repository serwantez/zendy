<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Form;

class Auth extends Form {

    public function init() {
        //ustawienie identyfikatora formularza
        $this->setAttrib('id', 'loginForm');
        //ustawienie akcji formularza na stronę bieżącą
        $this->setAction('');

        //kontrolka tekstowa do podania nazwy użytkownika
        $login = new Form\Element\Edit('username');
        $login
                ->setLabel('User name', 180)
                ->setWidth(180)
                ->setRequired(true)
        ;

        //kontrolka hasła
        $password = new Form\Element\Password('password');
        $password
                ->setLabel('Password', 180)
                ->setWidth(180)
                ->setRequired(true)
        ;

        //przycisk logowania
        $submit = new Form\Element\Submit('login');
        $submit
                ->setCaption('Log in')
        ;

        //przycisk rejestracji
        $signUp = new Form\Element\Link('signup');
        $signUp
                ->setHref('/auth/signup')
                ->setValue('Sign up')
                ->setTitle('Click to sign up')
                ->setTooltip()
        ;

        //link do formularza przywracania hasła
        $recover = new Form\Element\Link('recover');
        $recover
                ->setHref('/auth/recoverpassword')
                ->setValue("I don't remember my password")
                ->setTitle('Click to recover your password')
                ->setTooltip()
        ;

        //panel będący kontenerem dla przycisków i linku
        $btnPanel = new Form\Container\Panel('btnPanel');
        $btnPanel
                ->setHeight(40)
                ->addElements(array($submit, $signUp, $recover))
                ->addClasses(array(
                    Css::DIALOG_BUTTONPANE,
                    Css::WIDGET_CONTENT,
                    Css::HELPER_CLEARFIX
                ));

        //panel z nagłówkiem będący kontenerem dla kontrolek nazwy użytkownika i hasła 
        //oraz dla panelu przycisków
        $mainPanel = new Form\Container\Box('panel');
        $mainPanel
                ->setWidth(440)
                ->setTitle('Logging')
                ->addElements(array($login, $password))
                ->addContainer($btnPanel)
                ->setAlign(Css::ALIGN_CENTER)
        ;

        //dodanie głównego panelu do formularza
        $this->addContainer($mainPanel);
    }

}