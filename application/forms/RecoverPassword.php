<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Form\Container;
use ZendY\Form\Element;

class RecoverPassword extends \ZendY\Form {

    public function init() {
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

        $btnPanel = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'btnPanel',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_BOTTOM,
                    Container\Panel::PROPERTY_SPACE => array('value' => 0.2, 'unit' => 'em'),
                    Container\Panel::PROPERTY_HEIGHT => array('value' => 3.2, 'unit' => 'em'),
                    Container\Panel::PROPERTY_CLASSES => array(
                        Css::SCROLL_DISABLE
                    )
                ));
        $btnPanel
                ->addElements(array($submit))
        ;

        $panel = new Container\Box('panel');
        $panel->setWidth(450)
                ->setHeight(150)
                ->setTitle('Recovery password')
                ->addElements(array($email))
                ->addContainer($btnPanel)
                ->setAlign(Css::ALIGN_CENTER)
        ;

        $this->addContainer($panel);
    }

}

