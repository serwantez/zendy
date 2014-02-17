<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Form\Container;
use ZendY\Form\Element;

class Signup extends \ZendY\Form {

    public function init() {
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

        $btnPanel = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'btnPanel',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_BOTTOM,
                    Container\Panel::PROPERTY_SPACE => array('value' => 0.2, 'unit' => 'em'),
                    Container\Panel::PROPERTY_HEIGHT => array('value' => 3.2, 'unit' => 'em'),
                    Container\Panel::PROPERTY_CLASSES => array(
                        Css::SCROLL_DISABLE
                    )
                ));
        $btnPanel->addElements(array($submit));
        $box = new Container\Box(array(
                    'name' => 'box'
                ));
        $box->setWidth(450)
                ->setHeight(270)
                ->setTitle('Sign up')
                ->addElements(array($login, $password, $repeatpassword, $email, $repeatemail))
                ->addContainer($btnPanel)
                ->setAlign(Css::ALIGN_CENTER)
        ;

        $this->addContainer($box);
    }

}

