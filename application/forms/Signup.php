<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Form\Container;
use ZendY\Form\Element;

class Signup extends \ZendY\Form {

    public function init() {
        $this->setAttrib('id', 'signupForm');
        $this->setAction('');

        $login = new Element\RegisterLogin('username');
        $login
                ->setLabel('User name', 180)
                ->setTableAndField('user', 'username')
                ->setWidth(180)
                ->setFocus()
        ;

        $password = new Element\Password('password');
        $password
                ->setLabel('Password', 180)
                ->setWidth(180)
                ->setRequired(true)
                ->addValidator(new \Zend_Validate_StringLength(array('min' => 6)))
        ;

        $repeatpassword = new Element\RepeatPassword('repeatpassword');
        $repeatpassword
                ->setLabel('Repeat password', 180)
                ->setControl($password)
                ->setWidth(180)
        ;

        $email = new Element\RegisterEmail('email');
        $email
                ->setLabel('E-mail', 180)
                ->setTableAndField('user', 'email')
                ->setWidth(180)
        ;

        $repeatemail = new Element\RepeatEmail('repeatemail');
        $repeatemail
                ->setLabel('Repeat e-mail', 180)
                ->setControl($email)
                ->setWidth(180)
        ;

        $submit = new Element\Submit('signup');
        $submit
                ->setCaption('Sign up')
        ;

        $btnPanel = new Container\Panel('btnPanel');
        $btnPanel
                ->setHeight(35)
                ->addElements(array($submit))
                ->addClasses(array(
                    Css::DIALOG_BUTTONPANE,
                    Css::WIDGET_CONTENT,
                    Css::HELPER_CLEARFIX
                ))
                ->setAlign(Css::ALIGN_BOTTOM);

        $panel = new Container\Box('panel');
        $panel->setWidth(450)
                ->setHeight(230)
                ->setTitle('Sign up')
                ->addElements(array($login, $password, $repeatpassword, $email, $repeatemail))
                ->addContainer($btnPanel)
                ->setAlign(Css::ALIGN_CENTER)
        ;

        $this->addContainer($panel);
    }

}

