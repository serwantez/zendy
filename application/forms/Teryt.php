<?php

namespace Application\Form;

use Application\Model;
use ZendY\Css;
use ZendY\Form\Element;
use ZendY\Form\Container;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet;
use ZendY\Db\Form;
use ZendY\Db\Form\Element as DbElement;

/**
 * Formularz demonstracyjny z podziałem terytorialnym Polski
 *
 * @author Piotr Zając
 */
class Teryt extends Form {

    public function init() {
        //zbiory i źródła danych
        $dataSetV = new DataSet\Table(array(
                    DataSet\Table::PROPERTY_NAME => 'voivodship',
                    DataSet\Table::PROPERTY_TABLENAME => 'voivodship',
                    DataSet\Table::PROPERTY_PRIMARY => 'id',
                    DataSet\Table::PROPERTY_READONLY => true,
                ));

        $dataSourceV = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'voivodshipSource',
                    DataSource::PROPERTY_DATASET => $dataSetV,
                    DataSource::PROPERTY_DIALOG => false
                ));

        $dataSetP = new DataSet\Table(array(
                    DataSet\Table::PROPERTY_NAME => 'powiat',
                    DataSet\Table::PROPERTY_TABLENAME => 'powiat',
                    DataSet\Table::PROPERTY_PRIMARY => 'id',
                    DataSet\Table::PROPERTY_READONLY => true,
                    DataSet\Table::PROPERTY_MASTER => array(
                        array(
                            'masterSource' => $dataSourceV,
                            'masterField' => 'id',
                            'detailField' => 'woj'
                    )),
                ));

        $dataSourceP = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'powiatSource',
                    DataSource::PROPERTY_DATASET => $dataSetP
                ));

        $dataSetG = new DataSet\Table(array(
                    DataSet\Table::PROPERTY_NAME => 'gmina',
                    DataSet\Table::PROPERTY_TABLENAME => 'gmina',
                    DataSet\Table::PROPERTY_PRIMARY => 'id',
                    DataSet\Table::PROPERTY_READONLY => true,
                    DataSet\Table::PROPERTY_MASTER => array(
                        array(
                            'masterSource' => $dataSourceP,
                            'masterField' => 'id',
                            'detailField' => 'wojpow'
                    )),
                ));

        $dataSourceG = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'gminaSource',
                    DataSource::PROPERTY_DATASET => $dataSetG
                ));

        $dataSetS = new Model\Simc(array(
                    DataSet\Table::PROPERTY_MASTER => array(
                        array(
                            'masterSource' => $dataSourceG,
                            'masterField' => 'id',
                            'detailField' => 'teryt'
                    )),
                ));

        $dataSourceS = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'simcSource',
                    DataSource::PROPERTY_DATASET => $dataSetS
                ));

        //lista województw
        $listV = new DbElement\Listbox(array(
                    DbElement\Listbox::PROPERTY_NAME => 'voivodshipList',
                    DbElement\Listbox::PROPERTY_LISTSOURCE => $dataSourceV,
                    DbElement\Listbox::PROPERTY_LISTFIELD => array('nazwa'),
                    DbElement\Listbox::PROPERTY_KEYFIELD => 'id',
                    DbElement\Listbox::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                ));

        $boxV = new Container\Box(array(
                    Container\Box::PROPERTY_NAME => 'boxV',
                    Container\Box::PROPERTY_TITLE => 'Voivodship',
                    Container\Box::PROPERTY_ALIGN => Css::ALIGN_RIGHT,
                    Container\Box::PROPERTY_SPACE => 2,
                    Container\Box::PROPERTY_WIDTH => array(
                        'value' => 25,
                        'unit' => '%'
                    ),
                ));
        $boxV->setElements(array($listV));

        //lista powiatów
        $listP = new DbElement\Listbox(array(
                    DbElement\Listbox::PROPERTY_NAME => 'powiatList',
                    DbElement\Listbox::PROPERTY_LISTSOURCE => $dataSourceP,
                    DbElement\Listbox::PROPERTY_LISTFIELD => array('nazwa'),
                    DbElement\Listbox::PROPERTY_KEYFIELD => 'id',
                    DbElement\Listbox::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                ));

        $boxP = new Container\Box(array(
                    Container\Box::PROPERTY_NAME => 'boxP',
                    Container\Box::PROPERTY_TITLE => 'Powiat',
                    Container\Box::PROPERTY_ALIGN => Css::ALIGN_RIGHT,
                    Container\Box::PROPERTY_SPACE => 2,
                    Container\Box::PROPERTY_WIDTH => array(
                        'value' => 25,
                        'unit' => '%'
                    ),
                ));
        $boxP->setElements(array($listP));

        //lista gmin
        $listG = new DbElement\Listbox(array(
                    DbElement\Listbox::PROPERTY_NAME => 'gminaList',
                    DbElement\Listbox::PROPERTY_LISTSOURCE => $dataSourceG,
                    DbElement\Listbox::PROPERTY_LISTFIELD => array('nazwa', 'nazdod'),
                    DbElement\Listbox::PROPERTY_KEYFIELD => 'id',
                    DbElement\Listbox::PROPERTY_COLUMNSPACE => ' - ',
                    DbElement\Listbox::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                ));

        $boxG = new Container\Box(array(
                    Container\Box::PROPERTY_NAME => 'boxG',
                    Container\Box::PROPERTY_TITLE => 'Gmina',
                    Container\Box::PROPERTY_ALIGN => Css::ALIGN_RIGHT,
                    Container\Box::PROPERTY_SPACE => 2,
                    Container\Box::PROPERTY_WIDTH => array(
                        'value' => 25,
                        'unit' => '%'
                    ),
                ));
        $boxG->setElements(array($listG));

        //filtr formatujący
        $cityFilter = new \ZendY\Db\Filter();
        $cityFilter->addFilter('s.rm', 96);

        //lista miejscowości
        $listL = new DbElement\Listbox(array(
                    DbElement\Listbox::PROPERTY_NAME => 'simcList',
                    DbElement\Listbox::PROPERTY_LISTSOURCE => $dataSourceS,
                    DbElement\Listbox::PROPERTY_LISTFIELD => array('nazwa', 'nazwa_rm'),
                    DbElement\Listbox::PROPERTY_KEYFIELD => 'sym',
                    DbElement\Listbox::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                    DbElement\Listbox::PROPERTY_COLUMNSPACE => ' - ',
                    DbElement\Listbox::PROPERTY_CONDITIONALROWFORMATS => array(
                        array($cityFilter, 'row-bold')
                    ),
                ));

        $boxL = new Container\Box(array(
                    Container\Box::PROPERTY_NAME => 'boxL',
                    Container\Box::PROPERTY_TITLE => 'Locality',
                    Container\Box::PROPERTY_ALIGN => Css::ALIGN_RIGHT,
                    Container\Box::PROPERTY_SPACE => 2,
                    Container\Box::PROPERTY_WIDTH => array(
                        'value' => 25,
                        'unit' => '%'
                    ),
                ));
        $boxL->setElements(array($listL));

        //główny kontener list
        $panelMain = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelMain',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_CLIENT
                ));
        $panelMain->setContainers(array($boxV, $boxP, $boxG, $boxL));

        $code = new Element\TextFileView(array(
                    Element\TextFileView::PROPERTY_NAME => 'airportFormCode',
                    Element\TextFileView::PROPERTY_FILENAME => '../application/forms/Teryt.php',
                    Element\TextFileView::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                ));

        $description = new Element\DocFileView(array(
                    Element\TextFileView::PROPERTY_NAME => 'demoDescription',
                    Element\TextFileView::PROPERTY_FILENAME => '../application/views/scripts/demo/teryt_description.phtml',
                    Element\TextFileView::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                ));

        //zakładka opisu
        $tabPanel0 = new Container\TabPane(array(
                    Container\TabPane::PROPERTY_TITLE => 'Description',
                ));
        $tabPanel0->setElements(array($description));

        //zakładka demo
        $tabPanel1 = new Container\TabPane(array(
                    Container\TabPane::PROPERTY_TITLE => 'Demo',
                ));
        $tabPanel1->setContainers(array($panelMain));

        //zakładka kodu
        $tabPanel2 = new Container\TabPane(array(
                    Container\TabPane::PROPERTY_TITLE => 'Code',
                ));
        $tabPanel2->setElements(array($code))
        ;

        //główny kontener zakładek
        $tab = new Container\Tab(array(
                    Container\Tab::PROPERTY_NAME => 'tab1',
                    Container\Tab::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                ));
        $tab->setContainers(array($tabPanel0, $tabPanel1, $tabPanel2));

        $this->setContainers(array($tab));
    }

}

