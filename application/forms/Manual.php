<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container\Panel;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Db\Form\Container\EditDialog;
use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Form\Element;
use ZendY\Db\DataSet\App\Doc;
use ZendY\Db\DataSet\NestedTree;
use ZendY\Db\Form\Element as DbElement;

/**
 * Documentation
 *
 * Formularz Documentation
 *
 * @author Piotr Zając
 */
class Manual extends Form {

    private $_dataset;

    public function __construct($options = null) {
        $this->_dataset = new Doc(array(
                    'name' => 'doc'
                ));

        if (isset($options['search'])) {
            $this->_dataset->openAction();
            $this->_dataset->searchAction(array('searchValues' => array(
                    Doc::COL_ID => $options['search']
                    )));
            unset($options['search']);
        }
        parent::__construct($options);
    }

    public function init() {
        $dataSources['doc'] = new DataSource(array(
                    'name' => 'docSource',
                    'dataSet' => $this->_dataset
                ));

        //kontrolki
        $tree = new DbElement\Treeview('docs');
        $tree
                ->setListSource($dataSources['doc'])
                ->setKeyField(Doc::COL_ID)
                ->setListField(Doc::COL_NAME)
                ->addClass(Css::ALIGN_CLIENT)
        ;

        $panelMenu = new Panel();
        $panelMenu->addElement($tree)
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(300)
                ->setSpace()
        ;
        $contextMenu = new DbElement\ContextMenu('docsmenu');
        $contextMenu
                ->setDataSource($dataSources['doc'])
                ->setDelegate('.ui-tree-node-icon')
                ->setDataActions(array(
                    NestedTree::ACTION_ADD,
                    NestedTree::ACTION_ADDBEFORE,
                    NestedTree::ACTION_ADDUNDER,
                    NestedTree::ACTION_CUT,
                    NestedTree::ACTION_PASTEUNDER,
                    NestedTree::ACTION_PASTEBEFORE,
                    NestedTree::ACTION_PASTEAFTER,
                    DataSet::ACTION_REFRESH
                ));
        $panelMenu->addElement($contextMenu);


        $title = new DbElement\Text('titleText');
        $title->setDataSource($dataSources['doc'])
                ->setDataField(Doc::COL_FULLNAME)
                ->setAlign(Css::ALIGN_CLIENT)
                ->removeClass(Css::CORNER_ALL)
                ->addClass(Css::DOCUMENT_TITLE)
        ;

        $content = new DbElement\DocFileView('contentLongText');
        $content->setDataSource($dataSources['doc'])
                ->setDataField(Doc::COL_FILENAME)
                ->setAlign(Css::ALIGN_CLIENT)
                ->removeClass(Css::CORNER_ALL)
                ->addClass(Css::DOCUMENT)
        ;

        $panelTitle = new Panel();
        $panelTitle
                ->addElement($title)
                ->setAlign(Css::ALIGN_TOP)
                ->addClasses(array(Css::WIDGET_HEADER, Css::CORNER_TOP))
                ->setHeight(25);

        $panelContent = new Panel();
        $panelContent
                ->addElement($content)
                ->setAlign(Css::ALIGN_CLIENT);

        $actions = array(
            DataSet::ACTION_REFRESH,
        );

        $btnEdit = new Element\Button('editButton');
        $btnEdit
                ->setLabel('Details')
                ->setShortKey('F3')
        ;

        $nav = new Navigator();
        $nav
                ->setActions($actions)
                ->addElement($btnEdit)
                ->setDataSource($dataSources['doc'])
                ->setAlign(Css::ALIGN_BOTTOM)
                ->setSpace(array('value' => 0.2, 'unit' => 'em'))
        ;

        $panelMain = new Panel();
        $panelMain
                ->setAlign(Css::ALIGN_CLIENT)
                ->addContainer($panelTitle)
                ->addContainer($panelContent)
                ->setSpace()
        ;

        $this->setContainers(array(
            $panelMenu,
            $panelMain,
            $nav
        ));


        //edytor
        $i = 0;
        $elements[$i] = new DbElement\Edit('name');
        $elements[$i]
                ->setLabel('Document name')
                ->setDataSource($dataSources['doc'])
                ->setDataField(Doc::COL_NAME)
                ->setWidth(350)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('fullname');
        $elements[$i]
                ->setLabel('Full name')
                ->setDataSource($dataSources['doc'])
                ->setDataField(Doc::COL_FULLNAME)
                ->setWidth(350)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('filename');
        $elements[$i]
                ->setLabel('File name')
                ->setWidth(350)
                ->setDataSource($dataSources['doc'])
                ->setDataField(Doc::COL_FILENAME)
        ;

        $i++;
        $elements[$i] = new DbElement\Text('creation_date');
        $elements[$i]
                ->setLabel('Creation date')
                ->setDataSource($dataSources['doc'])
                ->setDataField(Doc::COL_CREATION_DATE)
                ->setWidth(200)
        ;

        $detailsPanel = new Panel();
        $detailsPanel->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $dialog = new EditDialog('docDetails');
        $dialog
                ->setDataSource($dataSources['doc'])
                ->setTitle('Document details')
                ->setWidth(550)
                ->setHeight(310)
                ->addContainer($detailsPanel)
                ->addOpener($btnEdit)
                ->addOpener($contextMenu->getItem(NestedTree::ACTION_ADD))
                ->addOpener($contextMenu->getItem(NestedTree::ACTION_ADDBEFORE))
                ->addOpener($contextMenu->getItem(NestedTree::ACTION_ADDUNDER))
        ;
        $this->addContainer($dialog);
    }

}
