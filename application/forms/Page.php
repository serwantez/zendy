<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container\Panel;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\Filter;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\DataSet\Editable;
use ZendY\Db\DataSet\NestedTree;
use ZendY\Db\DataSet\ClassConst;
use ZendY\Db\DataSet\ArraySet;
use ZendY\Db\DataSet\App\Page as DbPage;
use ZendY\Db\Form\Element as DbElement;

class Page extends Form {

    public function init() {
        $this->setAttrib('id', 'navi');
        $this->setAlign(Css::ALIGN_CLIENT);
        $this->setAjaxValidator(false);

        $actions = array(
            DataSet::ACTION_REFRESH,
            DataSet::ACTION_EXPORTEXCEL,
            Editable::ACTION_EDIT,
            Editable::ACTION_SAVE,
            Editable::ACTION_DELETE,
            Editable::ACTION_CANCEL,
            DbPage::ACTION_CREATEPAGE
        );


        $table = new DbPage('page');
        $dataSources[0] = new DataSource('menuSource', $table);

        $dataSet = new ArraySet('visibility');
        $dataSet->setData(array(
                    array('id' => 0, 'flag' => 'no'),
                    array('id' => 1, 'flag' => 'yes')
                ))
                ->setPrimary('id');
        $dataSourceVisibility = new DataSource('visibilitySource', $dataSet);

        $listElement[0] = new DbElement\Treeview('pageList');
        $listElement[0]
                ->setListSource($dataSources[0])
                ->setKeyField(DbPage::COL_ID)
                ->setListField(array(DbPage::COL_LABEL))
                ->setIconField(DbPage::COL_CLASS)
                ->addClass(Css::ALIGN_CLIENT)
        ;

        $panel1 = new Panel();
        $panel1->addElement($listElement[0])
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(300)
        ;
        $contextMenu = new DbElement\ContextMenu('treemenu');
        $contextMenu
                ->setDataSource($dataSources[0])
                ->setDelegate('.ui-tree-node-icon')
                ->setDataActions(array(
                    array('action' => NestedTree::ACTION_ADDBEFORE),
                    array('action' => Editable::ACTION_ADD),
                    array('action' => NestedTree::ACTION_ADDUNDER),
                    array('action' => NestedTree::ACTION_CUT),
                    array('action' => NestedTree::ACTION_PASTEUNDER),
                    array('action' => NestedTree::ACTION_PASTEBEFORE),
                    array('action' => NestedTree::ACTION_PASTEAFTER)
                ));
        $panel1->addElement($contextMenu);
        $this->addContainer($panel1);


        $elements[0] = new DbElement\Edit('label');
        $elements[0]
                ->setDataSource($dataSources[0])
                ->setDataField(DbPage::COL_LABEL)
                ->setLabel('Name');

        $elements[1] = new DbElement\Edit('uri');
        $elements[1]
                ->setDataSource($dataSources[0])
                ->setDataField(DbPage::COL_URI)
                ->setLabel('Uri')
                ->setWidth(200);

        $elements[2] = new DbElement\Edit('resource');
        $elements[2]
                ->setDataSource($dataSources[0])
                ->setDataField(DbPage::COL_RESOURCE)
                ->setLabel('Resource')
                ->setWidth(150);

        $elements[3] = new DbElement\Edit('privilege');
        $elements[3]
                ->setDataSource($dataSources[0])
                ->setDataField(DbPage::COL_PRIVILEGE)
                ->setLabel('Privilege')
                ->setWidth(150);

        $iconSet = new ClassConst('iconSet', '\ZendY\Css');
        $iconSet->setPrimary(ClassConst::COL_VALUE)
                ->sortAction(array('field' => ClassConst::COL_VALUE));
        $iconFilter = new Filter();
        $iconFilter->addFilter(ClassConst::COL_NAME, 'ICON_', DataSet::OPERATOR_BEGIN);
        $iconSet->filterAction(array('filter' => $iconFilter));
        //print_r($cc->getItems());
        //exit;
        $iconSource = new DataSource('iconSource', $iconSet);

        $elements[4] = new DbElement\IconCombobox('icon');
        $elements[4]
                ->setDataSource($dataSources[0])
                ->setDataField(DbPage::COL_CLASS)
                ->setListSource($iconSource)
                ->setKeyField(ClassConst::COL_VALUE)
                ->setListField(ClassConst::COL_VALUE)
                ->setLabel('Icon')
                ->setWidth(150)
                ->setStaticRender()
        ;

        $elements[5] = new DbElement\Radio('visible');
        $elements[5]
                ->setDataSource($dataSources[0])
                ->setDataField(DbPage::COL_VISIBLE)
                ->setListSource($dataSourceVisibility)
                ->setKeyField('id')
                ->setListField('flag')
                ->setLabel('Visible')
                ->setWidth(150)
                ->setStaticRender()
        ;

        $panel2 = new Panel();
        $panel2->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        $this->addContainer($panel2);

        $nav = new Navigator();
        $nav->setActions($actions)
                ->setDataSource($dataSources[0])
                ->setHeight(40)
                ->setAlign(Css::ALIGN_BOTTOM)
        ;

        $this->addContainer($nav);
    }

}

