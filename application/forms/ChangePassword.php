<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Form\Container;
use ZendY\Form\Element;

class ChangePassword extends \ZendY\Form {

    public function init() {
        $this->setAttrib('id', 'changePasswordForm');
        $this->setAction('');

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
                ->setHeight(150)
                ->setTitle('Changing password')
                ->addElements(array($password, $repeatpassword))
                ->addContainer($btnPanel)
                ->setAlign(Css::ALIGN_CENTER)
        ;

        $this->addContainer($panel);
    }

}

