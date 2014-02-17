<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Form\Container;
use ZendY\Form\Element;

class ChangePassword extends \ZendY\Form {

    public function init() {
        $password = new Element\Password('password');
        $password
                ->setLabel('New password', 180)
                ->setWidth(180)
                ->setRequired(true)
                ->addValidator(new \Zend_Validate_StringLength(array('min' => 6)))
        ;

        $repeatpassword = new Element\RepeatPassword('repeatpassword');
        $repeatpassword
                ->setLabel('Repeat new password', 180)
                ->setControl($password)
                ->setWidth(180)
        ;

        $submit = new Element\Submit('changePassword');
        $submit
                ->setCaption('Change password')
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

        $panel = new Container\Box(array(
                    'name' => 'panel'
                ));
        $panel->setWidth(450)
                ->setHeight(180)
                ->setTitle('Changing password')
                ->addElements(array($password, $repeatpassword))
                ->addContainer($btnPanel)
                ->setAlign(Css::ALIGN_CENTER)
        ;

        $this->addContainer($panel);
    }

}

