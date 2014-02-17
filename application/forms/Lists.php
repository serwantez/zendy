<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container;
use ZendY\Db\Form\Container as DbContainer;
use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet;
use ZendY\Db\DataSet\App;
use ZendY\Form\Element;
use ZendY\Db\Form\Element as DbElement;

class Lists extends Form {

    public function init() {
        $dataSetL = new App\Lists(array(
                    'name' => 'lists'
                ));
        $dataSourceLists = new DataSource(array(
                    'name' => 'listsSource',
                    'dataSet' => $dataSetL
                ));

        $dataSetLI = new App\ListItem(array(
                    'name' => 'list'
                ));
        $dataSetLI->addMaster($dataSourceLists
                , App\Lists::COL_ID
                , App\ListItem::COL_LIST_ID);
        $dataSourceListItems = new DataSource(array(
                    'name' => 'listSource',
                    'dataSet' => $dataSetLI
                ));

        $comboLists = new DbElement\Combobox('lists');
        $comboLists
                ->setListSource($dataSourceLists)
                ->setKeyField(App\Lists::COL_ID)
                ->setListField(App\Lists::COL_NAME)
                ->setWidth(240)
                ->removeDecorator('Section')
        ;

        $btnOpenLists = new Element\IconButton('openLists');
        $btnOpenLists
                ->setLabel('Lists')
                ->setIcons(Css::ICON_FOLDEROPEN);

        $comboListItems = new DbElement\SortableListbox('listItems');
        $comboListItems
                ->setListSource($dataSourceListItems)
                ->setListField(array(App\ListItem::COL_FLAG, App\ListItem::COL_NAME))
                ->setKeyField(App\ListItem::COL_ITEM_ID)
                ->addClass(Css::ALIGN_CLIENT)
        ;

        $panel7 = new Container\Panel();
        $panel7->addElement($comboLists)
                ->addElement($btnOpenLists)
                ->setHeight(40)
                ->setAlign(Css::ALIGN_TOP);

        $panel8 = new Container\Panel();
        $panel8->addElement($comboListItems)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $panel1 = new Container\Panel();
        $panel1->addContainer($panel7)
                ->addContainer($panel8)
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(300)
                ->setSpace()
        ;
        $this->addContainer($panel1);

        $i = 0;
        $elements[$i] = new DbElement\SpinEdit('item_id');
        $elements[$i]
                ->setLabel('Id')
                ->setDataSource($dataSourceListItems)
                ->setDataField(App\ListItem::COL_ITEM_ID)
                ->setWidth(50)
                ->setFocus()
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('flag');
        $elements[$i]
                ->setLabel('Code')
                ->setDataSource($dataSourceListItems)
                ->setDataField(App\ListItem::COL_FLAG)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('name');
        $elements[$i]
                ->setLabel('Name')
                ->setDataSource($dataSourceListItems)
                ->setDataField(App\ListItem::COL_NAME)
                ->setWidth(250)
        ;

        $panel2 = new Container\Box();
        $panel2
                ->setTitle('List element')
                ->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace()
        ;
        $this->addContainer($panel2);


        $actions = array(
            DataSet\Base::ACTION_FIRST,
            DataSet\Base::ACTION_PREVIOUS,
            DataSet\Base::ACTION_NEXT,
            DataSet\Base::ACTION_LAST,
            DataSet\Base::ACTION_REFRESH,
            DataSet\Base::ACTION_EXPORTEXCEL,
            DataSet\Base::ACTION_PRINT,
            array('action' => DataSet\Editable::ACTION_ADD, 'shortkey' => 'Ctrl+N'),
            array('action' => DataSet\Editable::ACTION_EDIT, 'shortkey' => 'F3'),
            array('action' => DataSet\Editable::ACTION_SAVE, 'shortkey' => 'Ctrl+S'),
            DataSet\Editable::ACTION_DELETE,
            DataSet\Editable::ACTION_CANCEL
        );
        $nav = new DbContainer\Navigator();
        $nav
                ->setActions($actions)
                ->setDataSource($dataSourceListItems)
                ->setSpace(array('value' => 0.2, 'unit' => 'em'))
        ;

        $this->addContainer($nav);

        $listId = new DbElement\Text('list_id');
        $listId
                ->setLabel('List ID')
                ->setDataSource($dataSourceLists)
                ->setDataField(App\Lists::COL_ID)
        ;

        $listName = new DbElement\Edit('list_name');
        $listName
                ->setLabel('List name')
                ->setDataSource($dataSourceLists)
                ->setDataField(App\Lists::COL_NAME)
                ->setWidth(250)
        ;

        $detailsPanel = new Container\Panel();
        $detailsPanel
                ->addElement($listId)
                ->addElement($listName)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $dialog = new DbContainer\EditDialog(array(
                    'name' => 'listsDetails'
                ));
        $dialog
                ->setDataSource($dataSourceLists)
                ->setTitle('Lists details')
                ->setWidth(500)
                ->setHeight(180)
                ->addContainer($detailsPanel)
                ->addOpener($btnOpenLists)
        ;
        $this->addContainer($dialog);
    }

}
