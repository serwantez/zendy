<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Db\Form\Container\FilterDialog;
use ZendY\Css;
use ZendY\Form\Element\Grid\Column;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\DataSet\ArraySet;
use ZendY\Db\DataSet\Editable;
use ZendY\Db\DataSet\App\Page;
use ZendY\Db\DataSet\App\Role;
use ZendY\Db\DataSet\App\Rule as DbRule;
use ZendY\Form\Element;
use ZendY\Db\Form\Element as DbElement;

class Rule extends Form {

    public function init() {
        $dataSet = new DbRule(array(
                    'name' => 'rule'
                ));
        $dataSourceDbRule = new DataSource(array(
                    'name' => 'ruleSource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new Role(array(
                    'name' => 'role'
                ));
        $dataSourceRole = new DataSource(array(
                    'name' => 'roleSource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new Page(array(
                    'name' => 'page'
                ));
        $dataSourcePage = new DataSource(array(
                    'name' => 'pageSource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new ArraySet(array(
                    'name' => 'rule_type'
                ));
        $dataSet->setData(array(
                    array('id' => 0, 'type' => 'deny'),
                    array('id' => 1, 'type' => 'allow')
                ))
                ->setPrimary('id');
        $dataSourceDbRuleType = new DataSource(array(
                    'name' => 'ruleTypeSource',
                    'dataSet' => $dataSet
                ));

        $grid = new DbElement\Grid('rulesGrid');
        $grid
                ->setListSource($dataSourceDbRule)
                ->setKeyField(DbRule::COL_ID)
                ->addColumn(new Column(
                                array(
                                    'name' => DbRule::COL_ID,
                                    'label' => 'ID',
                                    'width' => 40,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbRule::COL_ROLE_NAME,
                                    'label' => 'Role name',
                                    'width' => 150
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbRule::COL_RESOURCE,
                                    'label' => 'Resource name',
                                    'width' => 150
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbRule::COL_PRIVILEGE,
                                    'label' => 'Privilege',
                                    'width' => 150
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbRule::COL_ASSERT,
                                    'label' => 'Assertion class',
                                    'width' => 150
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbRule::COL_RULE_TYPE,
                                    'label' => 'Rule type',
                                    'width' => 50,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_CENTER,
                                    'decorators' => array(
                                        array('Icon', array('icons' => array(0 => 'ui-icon-cancel', 1 => 'ui-icon-check')))
                                    )
                        )))
                ->setAlign(Css::ALIGN_CLIENT)
                ->setPager(30)
        ;

        $panelp = new Container\Panel();
        $panelp->addElement($grid)
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace();

        $this->addContainer($panelp);

        $element[0] = new DbElement\Treeview('roleList');
        $element[0]
                ->setDataSource($dataSourceDbRule)
                ->setDataField(DbRule::COL_ROLE_ID)
                ->setListSource($dataSourceRole)
                ->setKeyField(Role::COL_ID)
                ->setListField(array(Role::COL_NAME))
                ->setIconField(Role::COL_CLASS)
                ->setWidth(200)
                ->setHeight(200)
                ->setLabel('Role', 100)
        ;

        $element[1] = new DbElement\Treeview('pageList');
        $element[1]
                ->setDataSource($dataSourceDbRule)
                ->setDataField(DbRule::COL_PAGE_ID)
                ->setListSource($dataSourcePage)
                ->setKeyField(Page::COL_ID)
                ->setListField(array(Page::COL_LABEL))
                ->setIconField(Page::COL_CLASS)
                ->setWidth(200)
                ->setHeight(200)
                ->setLabel('Resource', 100)
        ;

        $element[2] = new DbElement\Edit('assert');
        $element[2]
                ->setDataSource($dataSourceDbRule)
                ->setDataField(DbRule::COL_ASSERT)
                ->setLabel('Assertion class', 100)
        ;

        $element[3] = new DbElement\Radio('rule_type');
        $element[3]
                ->setDataSource($dataSourceDbRule)
                ->setDataField(DbRule::COL_RULE_TYPE)
                ->setListSource($dataSourceDbRuleType)
                ->setListField('type')
                ->setKeyField('id')
                ->setStaticRender()
                ->setLabel('Rule type', 100)
        ;

        $panelData = new Container\Box();
        $panelData
                ->setTitle('Rule data')
                ->addElements($element)
                ->setWidth(360)
                ->setAlign(Css::ALIGN_RIGHT)
                ->addClass(Css::SCROLL_AUTO)
                ->setSpace();
        ;
        $this->addContainer($panelData);

        $btnOpenFilter = new Element\IconButton('btnOpenFilter');
        $btnOpenFilter
                ->setLabel('Filter')
                ->setShortKey('Ctrl+F')
                ->setIcons(Css::ICON_SEARCH)
        ;

        $actions = array(
            DataSet::ACTION_FIRST,
            DataSet::ACTION_PREVIOUS,
            DataSet::ACTION_NEXT,
            DataSet::ACTION_LAST,
            DataSet::ACTION_REFRESH,
            DataSet::ACTION_EXPORTEXCEL,
            DataSet::ACTION_PRINT,
            Editable::ACTION_ADD,
            Editable::ACTION_EDIT,
            Editable::ACTION_SAVE,
            Editable::ACTION_DELETE,
            Editable::ACTION_CANCEL
        );
        $nav = new Navigator();
        $nav->setActions($actions)
                ->addElement($btnOpenFilter)
                ->setDataSource($dataSourceDbRule)
                ->setSpace(array('value' => 0.2, 'unit' => 'em'));
        $this->addContainer($nav);

        // okno filtrowania
        $dialogDbRuleFilter = new FilterDialog('dialogDbRuleFilter');
        $panelDbRuleFilters = new Container\Panel('panelDbRuleFilters');
        $filterElements[0] = new DbElement\Filter\IconEdit('filterRole');
        $filterElements[0]
                ->setDataSource($dataSourceDbRule)
                ->setDataField(DbRule::COL_ROLE_NAME)
                ->setLabel('Role name')
        ;

        $filterElements[1] = new DbElement\Filter\IconEdit('filterResource');
        $filterElements[1]
                ->setDataSource($dataSourceDbRule)
                ->setDataField(DbRule::COL_RESOURCE)
                ->setLabel('Resource name')
        ;

        $panelDbRuleFilters
                ->addElements($filterElements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        $dialogDbRuleFilter
                ->setDataSource($dataSourceDbRule)
                ->setTitle('Calendar filter')
                ->setWidth(350)
                ->setHeight(250)
                ->addContainer($panelDbRuleFilters)
                ->addOpener($btnOpenFilter)
        ;
        $this->addContainer($dialogDbRuleFilter);
    }

}
