<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Form\Container;
use ZendY\Form\Element;

class RecoverPassword extends \ZendY\Form {

    public function init() {
        $this->setAttrib('id', 'recoverPasswordForm');
        $this->setAction('');

        $description = 'Give your e-mail address';
        $email = new Element\Email('email');
        $email
                ->setLabel('Email', 180)
                ->setWidth(180)
                ->setRequired(true)
                ->setDescription($description)
        ;

        $submit = new Element\Submit('recoverPassword');
        $submit
                ->setCaption('Recover password')
        ;

        $btnPanel = new Container\Panel('btnPanel');
        $btnPanel
                ->setHeight(40)
                ->addElements(array($submit))
                ->addClasses(array(
                    Css::DIALOG_BUTTONPANE,
                    Css::WIDGET_CONTENT,
                    Css::HELPER_CLEARFIX
                ));

        $panel = new Container\Box('panel');
        $panel->setWidth(450)
                //->setHeight(150)
                ->setTitle('Recovery password')
                ->addElements(array($email))
                ->addContainer($btnPanel)
                ->setAlign(Css::ALIGN_CENTER)
        ;

        $this->addContainer($panel);
    }

}

