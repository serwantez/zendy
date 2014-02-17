<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\Filter;
use ZendY\Db\DataSet;
use ZendY\Db\DataSet\App\Page as DbPage;
use ZendY\Db\Form\Element as DbElement;

class Page extends Form {

    public function init() {
        $actions = array(
            DataSet\Base::ACTION_REFRESH,
            DataSet\Base::ACTION_EXPORTEXCEL,
            DataSet\Editable::ACTION_EDIT,
            DataSet\Editable::ACTION_SAVE,
            DataSet\Editable::ACTION_DELETE,
            DataSet\Editable::ACTION_CANCEL,
            DbPage::ACTION_CREATEPAGE,
            DataSet\NestedTree::ACTION_CALCULATEPARENT
        );


        $dataSetP = new DbPage(array(
                    'name' => 'page'
                ));
        $dataSourcePage = new DataSource(array(
                    'name' => 'menuSource',
                    'dataSet' => $dataSetP
                ));

        $dataSet = new DataSet\ArraySet(array(
                    'name' => 'visibility',
                    'data' => array(
                        array('id' => 0, 'flag' => 'no'),
                        array('id' => 1, 'flag' => 'yes')
                    ),
                    'primary' => 'id',
                ));
        $dataSourceVisibility = new DataSource(array(
                    'name' => 'visibilitySource',
                    'dataSet' => $dataSet
                ));

        $iconSet = new DataSet\ClassConst(array(
                    DataSet\ClassConst::PROPERTY_NAME => 'iconSet',
                    DataSet\ClassConst::PROPERTY_CLASS => '\ZendY\Css',
                ));
        $iconSet->sortAction(array('field' => DataSet\ClassConst::COL_VALUE));
        $iconFilter = new Filter();
        $iconFilter->addFilter(DataSet\ClassConst::COL_NAME, 'ICON_', DataSet\Base::OPERATOR_BEGIN);
        $iconSet->filterAction(array('filter' => $iconFilter));

        $iconSource = new DataSource(array(
                    'name' => 'iconSource',
                    'dataSet' => $iconSet
                ));

        $listPage = new DbElement\Treeview('pageList');
        $listPage
                ->setListSource($dataSourcePage)
                ->setKeyField(DbPage::COL_ID)
                ->setListField(array(DbPage::COL_LABEL))
                ->setIconField(DbPage::COL_CLASS)
                ->addClass(Css::ALIGN_CLIENT)
        ;

        $contextMenu = new DbElement\ContextMenu('treemenu');
        $contextMenu
                ->setDataSource($dataSourcePage)
                ->setDelegate('.ui-tree-node-icon')
                ->setDataActions(array(
                    array('action' => DataSet\NestedTree::ACTION_ADDBEFORE),
                    array('action' => DataSet\Editable::ACTION_ADD),
                    array('action' => DataSet\NestedTree::ACTION_ADDUNDER),
                    array('action' => DataSet\NestedTree::ACTION_CUT),
                    array('action' => DataSet\NestedTree::ACTION_PASTEUNDER),
                    array('action' => DataSet\NestedTree::ACTION_PASTEBEFORE),
                    array('action' => DataSet\NestedTree::ACTION_PASTEAFTER)
                ));

        $panel1 = new Container\Panel();
        $panel1->setElements(array($listPage, $contextMenu))
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(300)
                ->setSpace()
        ;
        $this->addContainer($panel1);


        $elements[0] = new DbElement\Edit('label');
        $elements[0]
                ->setDataSource($dataSourcePage)
                ->setDataField(DbPage::COL_LABEL)
                ->setLabel('Name');

        $elements[1] = new DbElement\Edit('uri');
        $elements[1]
                ->setDataSource($dataSourcePage)
                ->setDataField(DbPage::COL_URI)
                ->setLabel('Uri')
                ->setWidth(200);

        $elements[2] = new DbElement\Edit('resource');
        $elements[2]
                ->setDataSource($dataSourcePage)
                ->setDataField(DbPage::COL_RESOURCE)
                ->setLabel('Resource')
                ->setWidth(150);

        $elements[3] = new DbElement\Edit('privilege');
        $elements[3]
                ->setDataSource($dataSourcePage)
                ->setDataField(DbPage::COL_PRIVILEGE)
                ->setLabel('Privilege')
                ->setWidth(150);

        $elements[4] = new DbElement\IconCombobox('icon');
        $elements[4]
                ->setDataSource($dataSourcePage)
                ->setDataField(DbPage::COL_CLASS)
                ->setListSource($iconSource)
                ->setKeyField(DataSet\ClassConst::COL_VALUE)
                ->setListField(DataSet\ClassConst::COL_VALUE)
                ->setLabel('Icon')
                ->setWidth(150)
                ->setStaticRender()
        ;

        $elements[5] = new DbElement\Radio('visible');
        $elements[5]
                ->setDataSource($dataSourcePage)
                ->setDataField(DbPage::COL_VISIBLE)
                ->setListSource($dataSourceVisibility)
                ->setKeyField('id')
                ->setListField('flag')
                ->setLabel('Visible')
                ->setWidth(150)
                ->setStaticRender()
        ;

        $panel2 = new Container\Box();
        $panel2
                ->setTitle('Page data')
                ->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace()
        ;
        $this->addContainer($panel2);

        $nav = new Navigator();
        $nav
                ->setActions($actions)
                ->setDataSource($dataSourcePage)
                ->setSpace(array('value' => 0.2, 'unit' => 'em'))
        ;

        $this->addContainer($nav);
    }

}

