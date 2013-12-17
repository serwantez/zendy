<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container\Panel;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Db\Form\Container\EditDialog;
use ZendY\Css;
use ZendY\Form\Element\Grid\Column;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\DataSet\Editable;
use ZendY\Db\DataSet\App\Role;
use ZendY\Db\DataSet\App\User;
use ZendY\Db\DataSet\App\UserRole as DbUserRole;
use ZendY\Db\Form\Element as DbElement;

class UserRole extends Form {

    public function init() {
        $this->setAttrib('id', 'userRoleForm');
        $this->setAlign(Css::ALIGN_CLIENT);
        $this->setAjaxValidator(false);

        $dataSet = new DbUserRole('userrole');
        $dataSources[0] = new DataSource('userroleSource', $dataSet);

        $dataSourceRole = new DataSource('roleSource', new Role('role'));

        $dataSourceUser = new DataSource('userSource', new User('user'));


        //przyciski akcji
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
                ->setDataSource($dataSources[0])
                ->setDataAction(Editable::ACTION_ADD)
                ->setShortKey('Ctrl+N')
        ;

        $btnEdit = new DbElement\Button('editButton');
        $btnEdit
                ->setDataSource($dataSources[0])
                ->setDataAction(Editable::ACTION_EDIT)
                ->setShortKey('F3')
        ;

        //pozostałe
        $grid = new DbElement\Grid('userroleGrid');
        $grid
                ->setListSource($dataSources[0])
                ->setKeyField('id')
                ->addColumn(new Column(
                                DbUserRole::COL_ID,
                                array(
                                    'label' => 'ID',
                                    'width' => 40,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Column(
                                DbUserRole::COL_USER_NAME,
                                array(
                                    'label' => 'Username',
                                    'width' => 160,
                        )))
                ->addColumn(new Column(
                                DbUserRole::COL_ROLE_NAME,
                                array(
                                    'label' => 'Role name',
                                    'width' => 160,
                        )))
                ->setAlign(Css::ALIGN_CLIENT)
                ->setPager(30)
                ->setSorter()
                ->setJQueryParam(
                        \ZendY\Form\Element\Grid::PARAM_EVENT_DBLCLICKROW
                        , sprintf('$("#%s").trigger("click");', $btnEdit->getId())
                )
        ;

        $panel1 = new Panel();
        $panel1->addElement($grid)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        $this->addContainer($panel1);

        $elements[0] = new DbElement\Combobox('username');
        $elements[0]
                ->setDataSource($dataSources[0])
                ->setDataField(DbUserRole::COL_USER_ID)
                ->setListSource($dataSourceUser)
                ->setKeyField(User::COL_ID)
                ->setListField(array(User::COL_USERNAME))
                ->setWidth(220)
                ->setLabel('Username', 120);

        $elements[1] = new DbElement\Treeview('roleList');
        $elements[1]
                ->setDataSource($dataSources[0])
                ->setDataField(DbUserRole::COL_ROLE_ID)
                ->setListSource($dataSourceRole)
                ->setKeyField(Role::COL_ID)
                ->setListField(array(Role::COL_NAME))
                ->setIconField(Role::COL_CLASS)
                ->setWidth(220)
                ->setHeight(200)
                ->setLabel('Role', 120)
        ;

        $detailsPanel = new Panel();
        $detailsPanel->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;


        $nav = new Navigator();
        $nav->setActions($actions)
                ->setDataSource($dataSources[0])
                ->addElement($btnAdd)
                ->addElement($btnEdit)
                ->setHeight(40)
                ->setAlign(Css::ALIGN_BOTTOM)
        ;

        $this->addContainer($nav);

        $dialog = new EditDialog('userDetails');
        $dialog
                ->setDataSource($dataSources[0])
                ->setTitle('User details')
                ->setWidth(400)
                ->setHeight(320)
                ->addContainer($detailsPanel)
                ->addOpener($btnEdit)
                ->addOpener($btnAdd)
        ;

        $this->addContainer($dialog);
    }

}
