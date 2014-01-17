<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Db;
use ZendY\Db\DataSet\App\User;
use ZendY\Db\DataSource;
use ZendY\Form\Container;
use ZendY\Db\Form\Container as DbContainer;
use ZendY\Db\Form;
use ZendY\Db\Form\Element as DbElement;

/**
 * Formularz profilu użytkownika
 *
 * @author Piotr Zając
 */
class Profile extends Form {

    public function init() {
        $this->setAttrib('id', 'profileForm');
        $this->setAction('');
        //$this->setAlign(Css::ALIGN_CLIENT);

        $dataSet = new User('user');
        $auth = \Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity()->id;
        } else {
            $user = 0;
        }
        $currentUser = new Db\Filter();
        $currentUser->addFilter(User::COL_ID, $user);
        $dataSet->filterAction(array('filter' => $currentUser));
        $dataSource = new DataSource('dataSourceUser', $dataSet);

        $photo = new DbElement\ImageView('photo');
        $photo
                ->setDataSource($dataSource)
                ->setDataField(User::COL_PHOTO)
                ->setWidth(110)
                ->setUploadDirectory('/application/images/uploaded/users/')
                ->setFit(true)
                ->addClass(Css::ALIGN_HCENTER)
                ->removeDecorator('Section')
        ;

        $panelPhoto = new Container\Panel('panelPhoto');
        $panelPhoto
                ->addElement($photo)
                ->setAlign(Css::ALIGN_TOP)
                ->setHeight(130);

        $login = new DbElement\Text('login');
        $login
                ->setDataSource($dataSource)
                ->setDataField(User::COL_USERNAME)
                ->addClasses(array(
                    Css::TEXT_ALIGN_HORIZONTAL_CENTER,
                ))
                ->setLabel('Login', 80)
        ;

        $addition = new DbElement\Text('addition');
        $addition
                ->setDataSource($dataSource)
                ->setDataField(User::COL_ADDITION_TIME)
                ->addClasses(array(
                    Css::TEXT_ALIGN_HORIZONTAL_CENTER,
                ))
                ->setLabel('Addition time', 80)
        ;

        $panelConst = new Container\Panel('panelConst');
        $panelConst
                ->addElements(array($login, $addition))
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $panelLeft = new Container\Panel('panelLeft');
        $panelLeft
                ->setWidth(210)
                ->addContainers(array($panelPhoto, $panelConst))
                ->setWidgetClass(Css::WIDGET_CONTENT)
                ->setAlign(Css::ALIGN_LEFT)
                ->setSpace()
        ;

        $elements[0] = new DbElement\Edit('firstname');
        $elements[0]
                ->setDataSource($dataSource)
                ->setDataField(User::COL_FIRSTNAME)
                ->setLabel('Firstname')
        ;

        $elements[1] = new DbElement\Edit('surname');
        $elements[1]
                ->setDataSource($dataSource)
                ->setDataField(User::COL_SURNAME)
                ->setLabel('Surname')
        ;

        $elements[2] = new DbElement\Email('email');
        $elements[2]
                ->setDataSource($dataSource)
                ->setDataField(User::COL_EMAIL)
                ->setLabel('Email')
        ;


        $panelMain = new Container\Box('panelMain');
        $panelMain
                ->setTitle('User data')
                ->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace()
        ;

        $btnEdit = new DbElement\Button('btnEdit');
        $btnEdit
                ->setDataSource($dataSource)
                ->setDataAction(Db\DataSet\Editable::ACTION_EDIT)
                ->setVisibleText(true)
        ;

        $btnSave = new DbElement\Button('btnSave');
        $btnSave
                ->setDataSource($dataSource)
                ->setDataAction(Db\DataSet\Editable::ACTION_SAVE)
                ->setVisibleText(true)
        ;

        $panelBottom = new DbContainer\Navigator('panelBottom');
        $panelBottom
                ->setDataSource($dataSource)
                ->addElements(array($btnEdit, $btnSave))
                ->setHeight(40)
                ->setSpace()
        ;

        $panelAll = new Container\Panel('panelAll');
        $panelAll
                ->addContainers(array($panelLeft, $panelMain, $panelBottom))
                //->setWidth(600)
                //->setHeight(400)
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace()
        ;

        $this->addContainer($panelAll);
    }

}

