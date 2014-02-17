<?php

namespace Application\Form;

use ZendY\Form\Container\Panel;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Db\Form\Container\EditDialog;
use ZendY\Db\Form;
use ZendY\Css;
use ZendY\Form\Element\Grid\Column;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\DataSet\Editable;
use ZendY\Db\DataSet\ArraySet;
use ZendY\Db\DataSet\App\User as DbUser;
use ZendY\Db\Form\Element as DbElement;

class User extends Form {

    public function init() {
        $dataSetU = new DbUser(array(
                    'name' => 'user'
                ));
        $dataSourceDbUser = new DataSource(array(
                    'name' => 'userSource',
                    'dataSet' => $dataSetU
                ));

        $dataSetA = new ArraySet(array(
                    'name' => 'activity',
                    'data' => array(
                        array('id' => 0, 'flag' => 'no'),
                        array('id' => 1, 'flag' => 'yes')
                    ),
                    'primary' => 'id'
                ));
        $dataSourceActivity = new DataSource(array(
                    'name' => 'activitySource',
                    'dataSet' => $dataSetA
                ));

        $grid = new DbElement\Grid('userGrid');
        $grid
                ->setListSource($dataSourceDbUser)
                ->setKeyField('id')
                ->addColumn(new Column(
                                array(
                                    'name' => DbUser::COL_ID,
                                    'label' => 'ID',
                                    'width' => 40,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbUser::COL_USERNAME,
                                    'label' => 'User name',
                                    'width' => 140,
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbUser::COL_FIRSTNAME,
                                    'label' => 'Firstname',
                                    'width' => 140,
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbUser::COL_SURNAME,
                                    'label' => 'Surname',
                                    'width' => 140,
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbUser::COL_EMAIL,
                                    'label' => 'Email',
                                    'width' => 180,
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbUser::COL_ACTIVE,
                                    'label' => 'Active',
                                    'width' => 55,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_CENTER,
                                    'decorators' => array(
                                        array('Icon', array(
                                                'icons' => array(
                                                    0 => 'ui-icon-cancel',
                                                    1 => 'ui-icon-check'
                                                )
                                        ))
                                    )
                        )))
                ->setAlign(Css::ALIGN_CLIENT)
                ->setPager(30)
                ->setSorter()
        ;

        $panel1 = new Panel();
        $panel1->addElement($grid)
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace()
        ;

        $elements[0] = new DbElement\Edit('username');
        $elements[0]
                ->setDataSource($dataSourceDbUser)
                ->setDataField(DbUser::COL_USERNAME)
                ->setWidth(170)
                ->setLabel('User name');

        $elements[1] = new DbElement\Edit('firstname');
        $elements[1]
                ->setDataSource($dataSourceDbUser)
                ->setDataField(DbUser::COL_FIRSTNAME)
                ->setWidth(170)
                ->setLabel('Firstname');

        $elements[2] = new DbElement\Edit('surname');
        $elements[2]
                ->setDataSource($dataSourceDbUser)
                ->setDataField(DbUser::COL_SURNAME)
                ->setWidth(170)
                ->setLabel('Surname');

        $elements[3] = new DbElement\Email('email');
        $elements[3]
                ->setDataSource($dataSourceDbUser)
                ->setDataField(DbUser::COL_EMAIL)
                ->setWidth(170)
                ->setLabel('Email');

        $elements[4] = new DbElement\Radio('active');
        $elements[4]
                ->setListSource($dataSourceActivity)
                ->setListField('flag')
                ->setKeyField('id')
                ->setStaticRender()
                ->setDataSource($dataSourceDbUser)
                ->setDataField(DbUser::COL_ACTIVE)
                ->setLabel('Active');

        $detailsPanel = new Panel();
        $detailsPanel->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;


        $actions = array(
            DataSet::ACTION_FIRST,
            DataSet::ACTION_PREVIOUS,
            DataSet::ACTION_NEXT,
            DataSet::ACTION_LAST,
            DataSet::ACTION_REFRESH,
            DataSet::ACTION_EXPORTEXCEL,
            DataSet::ACTION_PRINT
        );

        $btnAdd = new DbElement\Button('addButton');
        $btnAdd
                ->setDataSource($dataSourceDbUser)
                ->setDataAction(Editable::ACTION_ADD)
                ->setShortKey('Ctrl+N')
        ;

        $btnEdit = new DbElement\Button('editButton');
        $btnEdit
                ->setDataSource($dataSourceDbUser)
                ->setDataAction(Editable::ACTION_EDIT)
                ->setShortKey('F3')
        ;

        $nav = new Navigator();
        $nav->setActions($actions)
                ->setDataSource($dataSourceDbUser)
                ->addElement($btnAdd)
                ->addElement($btnEdit)
                ->setAlign(Css::ALIGN_BOTTOM)
                ->setSpace(array('value' => 0.2, 'unit' => 'em'))
        ;

        $dialog = new EditDialog(array(
                    'name' => 'userDetails'
                ));
        $dialog
                ->setDataSource($dataSourceDbUser)
                ->setTitle('User details')
                ->setWidth(500)
                ->setHeight(300)
                ->addContainer($detailsPanel)
                ->addOpener($btnEdit)
                ->addOpener($btnAdd)
        ;

        $this->setContainers(array(
            $panel1,
            $nav,
            $dialog
        ));
    }

}
