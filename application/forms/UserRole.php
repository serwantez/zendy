<?php

namespace Application\Form;

use ZendY\Db\Form,
    ZendY\Form\Container\Panel,
    ZendY\Db\Form\Container\Navigator,
    ZendY\Db\Form\Container\EditDialog,
    ZendY\Css,
    ZendY\Form\Element\Grid\Column,
    ZendY\Db\DataSource,
    ZendY\Db\DataSet\Base as DataSet,
    ZendY\Db\DataSet\Editable,
    ZendY\Db\DataSet\App\Role,
    ZendY\Db\DataSet\App\User,
    ZendY\Db\DataSet\App\UserRole as DbUserRole,
    ZendY\Db\Form\Element as DbElement;

class UserRole extends Form {

    public function init() {
        $dataSet = new DbUserRole(array(
                    'name' => 'userRole'
                ));
        $dataSources[0] = new DataSource(array(
                    'name' => 'userRoleSource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new Role(array(
                    'name' => 'role'
                ));
        $dataSourceRole = new DataSource(array(
                    'name' => 'roleSource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new User(array(
                    'name' => 'user'
                ));
        $dataSourceUser = new DataSource(array(
                    'name' => 'userSource',
                    'dataSet' => $dataSet
                ));


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
                                array(
                                    'name' => DbUserRole::COL_ID,
                                    'label' => 'ID',
                                    'width' => 40,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbUserRole::COL_USER_NAME,
                                    'label' => 'Username',
                                    'width' => 160,
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbUserRole::COL_ROLE_NAME,
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
                ->setSpace()
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
                ->setLabel('Username');

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
                ->setLabel('Role')
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
                ->setSpace(array('value' => 0.2, 'unit' => 'em'))
        ;

        $this->addContainer($nav);

        $dialog = new EditDialog(array(
                    'name' => 'userDetails'
                ));
        $dialog
                ->setDataSource($dataSources[0])
                ->setTitle('User role details')
                ->setWidth(460)
                ->setHeight(360)
                ->addContainer($detailsPanel)
                ->addOpener($btnEdit)
                ->addOpener($btnAdd)
        ;

        $this->addContainer($dialog);
    }

}
