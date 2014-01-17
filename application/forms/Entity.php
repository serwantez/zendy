<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\DataSet\App\ListItem;
use ZendY\Db\DataSet\App\Entity as DbEntity;
use ZendY\Db\DataSet\App\Country;
use ZendY\Db\DataSet\NestedTree;
use ZendY\Db\DataSet\Editable;
use ZendY\Db\Form\Element as DbElement;

class Entity extends Form {

    public function init() {
        $this->setAttrib('id', 'entityForm');
        $this->setAlign(Css::ALIGN_CLIENT);
        $this->setEnctype(\Zend_Form::ENCTYPE_MULTIPART);
        $this->setAjaxValidator(false);

        $dataSet = new DbEntity('entity');
        $dataSources[0] = new DataSource('entitySource', $dataSet);

        $dataSet = new Country('country');
        $dataSources['country'] = new DataSource('countrySource', $dataSet);

        $dataSet = new ListItem('type');
        $dataSet->setList(13);
        $dataSources['type'] = new DataSource('typeSource', $dataSet);

        $treeView = new DbElement\Treeview('entities');
        $treeView
                ->setListSource($dataSources[0])
                ->setKeyField(DbEntity::COL_ID)
                ->setListField(array(DbEntity::COL_NAME))
                ->setDecorators(array(
                    array('UiWidgetMultiElement'),
                    array('HtmlTag', array('class' => Css::ALIGN_CLIENT))
                ))
                ->addClass(Css::ALIGN_CLIENT)
        ;

        $panel1 = new Container\Panel();
        $panel1->addElement($treeView)
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(450)
                ->setSpace()
        ;
        $contextMenu = new DbElement\ContextMenu('entitymenu');
        $contextMenu
                ->setDataSource($dataSources[0])
                ->setDelegate('.ui-tree-node-icon')
                ->setDataActions(array(
                    NestedTree::ACTION_ADDBEFORE,
                    Editable::ACTION_ADD,
                    NestedTree::ACTION_ADDUNDER,
                    NestedTree::ACTION_CUT,
                    NestedTree::ACTION_PASTEUNDER,
                    NestedTree::ACTION_PASTEBEFORE,
                    NestedTree::ACTION_PASTEAFTER
                ));
        $panel1->addElement($contextMenu);
        $this->addContainer($panel1);


        $i = 0;
        $elements[$i] = new DbElement\Edit('name');
        $elements[$i]
                ->setLabel('Name')
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_NAME)
                ->setWidth(350)
        ;

        $i++;
        $elements[$i] = new DbElement\Combobox('type');
        $elements[$i]
                ->setLabel('Type')
                ->setListSource($dataSources['type'])
                ->setListField(ListItem::COL_NAME)
                ->setKeyField(ListItem::COL_ITEM_ID)
                ->setStaticRender()
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_TYPE)
                ->setWidth(200)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('address');
        $elements[$i]
                ->setLabel('Address')
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_ADDRESS)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('postal_code');
        $elements[$i]
                ->setLabel('Postal code')
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_POSTAL_CODE)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('postal_locality');
        $elements[$i]
                ->setLabel('Postal locality')
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_POSTAL_LOCALITY)
                ->setWidth(200)
        ;

        $i++;
        $elements[$i] = new DbElement\Combobox('country_id');
        $elements[$i]
                ->setLabel('Country')
                ->setListSource($dataSources['country'])
                ->setListField(Country::COL_NAME)
                ->setKeyField(Country::COL_ID)
                ->setStaticRender()
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_COUNTRY_ID)
                ->setWidth(200)
        ;

        $i++;
        $elements[$i] = new DbElement\Email('email');
        $elements[$i]
                ->setLabel('Email')
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_EMAIL)
                ->setWidth(200)
        ;

        $i++;
        $elements[$i] = new DbElement\Url('website');
        $elements[$i]
                ->setLabel('Website')
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_WEBSITE)
                ->setWidth(200)
        ;

        $i++;
        $elements[$i] = new DbElement\Image('photo');
        $elements[$i]
                ->setLabel('Photo')
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_PHOTO)
                ->setFit(true)
                ->setWidth(250)
                ->setHeight(150)
        ;

        $i++;
        $elements[$i] = new DbElement\PointMap('coordinates');
        $elements[$i]
                ->setLabel('Coordinates')
                ->setDataSource($dataSources[0])
                ->setDataField(DbEntity::COL_COORDINATES)
                ->setWidth(450)
                ->setHeight(300)
        ;

        $panel2 = new Container\Box();
        $panel2
                ->setTitle('Entity data')
                ->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
                ->addClass(Css::SCROLL_AUTO)
                ->setSpace()
        ;
        $this->addContainer($panel2);



        $actions = array(
            DataSet::ACTION_REFRESH,
            DataSet::ACTION_EXPORTEXCEL,
            DataSet::ACTION_PRINT,
            array('action' => Editable::ACTION_EDIT, 'shortkey' => 'F3'),
            array('action' => Editable::ACTION_SAVE, 'shortkey' => 'Ctrl+S'),
            Editable::ACTION_DELETE,
            Editable::ACTION_CANCEL,
            NestedTree::ACTION_CALCULATEPARENT
        );
        $nav = new Navigator();
        $nav
                ->setActions($actions)
                ->setDataSource($dataSources[0])
                ->setSpace()
        ;

        $this->addContainer($nav);
    }

}

