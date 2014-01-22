<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace Application\Form;

use ZendY\Css;
use ZendY\Db\Form;
use ZendY\Db\DataSet\FilesTree;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\Form\Element as DbElement;
use ZendY\Form\Container\Panel;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Db\DataSource;

/**
 * Classpreview
 *
 * Formularz Classpreview
 *
 * @author Piotr Zając
 */
class Classpreview extends Form {

    private $_dataset;

    public function __construct($options = null, $sources = null) {
        $this->_dataset = new FilesTree('files');
        $this->_dataset->setPath('../library/ZendY');

        if (isset($options['search'])) {
            $this->_dataset->openAction();
            $this->_dataset->searchAction(array('searchValues' => array('id' => $options['search'])));
            unset($options['search']);
        }
        parent::__construct($options, $sources);
    }

    public function init() {
        //ustawienia ogólne
        $this->setAttrib('id', 'classPreviewForm');
        $this->setAlign(Css::ALIGN_CLIENT);
        $this->setAjaxValidator(false);

        //$files = $this->getSource('files');
        //zbiory danych
        $files = new DataSource('files', $this->_dataset);

        //kontrolki
        $tree = new DbElement\Treeview('treeviewFiles');
        $tree
                ->setListSource($files)
                ->setKeyField(FilesTree::COL_ID)
                ->setListField(FilesTree::COL_NAME)
                ->addClass(Css::ALIGN_CLIENT)
        ;

        $panelMenu = new Panel();
        $panelMenu->addElement($tree)
                ->setAlign(Css::ALIGN_LEFT)
                ->setSpace()
                ->setWidth(350)
        ;


        $title = new DbElement\Text('textName');
        $title->setDataSource($files)
                ->setDataField(FilesTree::COL_NAME)
                ->setAlign(Css::ALIGN_CLIENT)
                ->removeClass(Css::CORNER_ALL)
                ->addClass(Css::DOCUMENT_TITLE)
        ;

        $content = new DbElement\TextFileView('textFileViewContent');
        $content->setDataSource($files)
                ->setDataField(FilesTree::COL_FILEPATH)
                ->setAlign(Css::ALIGN_CLIENT)
                ->removeClass(Css::CORNER_ALL)
        ;

        $panelTitle = new Panel();
        $panelTitle
                ->addElement($title)
                ->setAlign(Css::ALIGN_TOP)
                ->addClasses(array(Css::WIDGET_HEADER, Css::CORNER_TOP))
                ->setHeight(25)
        ;

        $panelContent = new Panel();
        $panelContent
                ->addElement($content)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $actions = array(
            DataSet::ACTION_REFRESH,
            FilesTree::ACTION_DOWNLOAD,
            FilesTree::ACTION_DELETE
        );

        $nav = new Navigator();
        $nav
                ->setActions($actions)
                ->setDataSource($files)
        ;

        $panelMain = new Panel();
        $panelMain
                ->setAlign(Css::ALIGN_CLIENT)
                ->addContainer($panelTitle)
                ->addContainer($panelContent)
                ->addContainer($nav)
                ->setSpace()
        ;

        $this->addContainer($panelMenu);
        $this->addContainer($panelMain);
    }

}

