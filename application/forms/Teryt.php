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
        //ustawienia ogólne
        $this->setAttrib('id', 'teryt');
        $this->setAlign(Css::ALIGN_CLIENT);
        $this->setAjaxValidator(false);

        //zbiory i źródła danych
        $table = new DataSet\Table('voivodship');
        $table->setTableName('voivodship')
                ->setPrimary('id')
                ->setReadOnly(true)
        ;

        $dataSourceV = new DataSource('voivodshipSource');
        $dataSourceV->setDataSet($table)
                ->setDialog(false);

        $table = new DataSet\Table('powiat');
        $table->setTableName('powiat')
                ->setPrimary('id')
                ->setReadOnly(true)
                ->setMasterSource($dataSourceV)
                ->setMasterField('id')
                ->setIndexField('woj')
        ;

        $dataSourceP = new DataSource('powiatSource');
        $dataSourceP->setDataSet($table)
        ;

        $table = new DataSet\Table('gmina');
        $table->setTableName('gmina')
                ->setPrimary('id')
                ->setReadOnly(true)
                ->setMasterSource($dataSourceP)
                ->setMasterField('id')
                ->setIndexField('wojpow')
        ;

        $dataSourceG = new DataSource('gminaSource');
        $dataSourceG->setDataSet($table);

        $table = new Model\Simc('simc');
        $table->setMasterSource($dataSourceG)
                ->setMasterField('id')
                ->setIndexField('teryt')
        ;

        $dataSourceS = new DataSource('simcSource');
        $dataSourceS->setDataSet($table);


        //główny kontener list
        $panelMain = new Container\Panel();
        $panelMain->setAlign(Css::ALIGN_CLIENT);

        //lista województw
        $listV = new DbElement\Listbox('voivodshipList');
        $listV
                ->setListSource($dataSourceV)
                ->setKeyField('id')
                ->setListField(array('nazwa'))
                ->setAlign(Css::ALIGN_CLIENT)
                ->removeClass(Css::CORNER_ALL)
        ;

        $titleV = new Element\Text('voivodshipTitle');
        $titleV->setAlign(Css::ALIGN_CLIENT)
                ->setValue('Voivodship')
                ->addClass(Css::TEXT_ALIGN_HORIZONTAL_CENTER);

        $headerV = new Container\Panel('voivodshipHeader');
        $headerV->setHeight(25)
                ->setAlign(Css::ALIGN_TOP)
                ->addElement($titleV)
                ->addClasses(array(Css::WIDGET_HEADER, Css::CORNER_TOP));

        $bodyV = new Container\Panel();
        $bodyV->setAlign(Css::ALIGN_CLIENT)
                ->addElement($listV);

        $panelV = new Container\Panel();
        $panelV->addContainer($headerV)
                ->addContainer($bodyV)
                ->setAlign(Css::ALIGN_RIGHT)
                ->setWidth(25, '%')
                ->setSpace()
        ;
        $panelMain->addContainer($panelV);

        //lista powiatów
        $listP = new DbElement\Listbox('powiatList');
        $listP
                ->setListSource($dataSourceP)
                ->setKeyField('id')
                ->setListField(array('nazwa'))
                ->setAlign(Css::ALIGN_CLIENT)
                ->removeClass(Css::CORNER_ALL)
        ;

        $titleP = new Element\Text('powiatTitle');
        $titleP->setAlign(Css::ALIGN_CLIENT)
                ->setValue('Powiat')
                ->addClass(Css::TEXT_ALIGN_HORIZONTAL_CENTER);

        $headerP = new Container\Panel('powiatHeader');
        $headerP->setHeight(25)
                ->setAlign(Css::ALIGN_TOP)
                ->addElement($titleP)
                ->addClasses(array(Css::WIDGET_HEADER, Css::CORNER_TOP));

        $bodyP = new Container\Panel();
        $bodyP->setAlign(Css::ALIGN_CLIENT)
                ->addElement($listP);

        $panelP = new Container\Panel();
        $panelP->addContainer($headerP)
                ->addContainer($bodyP)
                ->setAlign(Css::ALIGN_RIGHT)
                ->setWidth(25, '%')
                ->setSpace()
        ;
        $panelMain->addContainer($panelP);


        //lista gmin
        $listG = new DbElement\Listbox('gminaList');
        $listG
                ->setListSource($dataSourceG)
                ->setKeyField('id')
                ->setListField(array('nazwa', 'nazdod'))
                ->setColumnSpace(' - ')
                ->setAlign(Css::ALIGN_CLIENT)
                ->removeClass(Css::CORNER_ALL)
        ;

        $titleG = new Element\Text('gminaTitle');
        $titleG->setAlign(Css::ALIGN_CLIENT)
                ->setValue('Gmina')
                ->addClass(Css::TEXT_ALIGN_HORIZONTAL_CENTER);

        $headerG = new Container\Panel('gminaHeader');
        $headerG->setHeight(25)
                ->setAlign(Css::ALIGN_TOP)
                ->addElement($titleG)
                ->addClasses(array(Css::WIDGET_HEADER, Css::CORNER_TOP));

        $bodyG = new Container\Panel();
        $bodyG->setAlign(Css::ALIGN_CLIENT)
                ->addElement($listG);

        $panelG = new Container\Panel();
        $panelG->addContainer($headerG)
                ->addContainer($bodyG)
                ->setAlign(Css::ALIGN_RIGHT)
                ->setWidth(25, '%')
                ->setSpace()
        ;
        $panelMain->addContainer($panelG);


        //lista miejscowości
        $listL = new DbElement\Listbox('simcList');
        //filtr formatujący
        $cityFilter = new \ZendY\Db\Filter();
        $cityFilter->addFilter('s.rm', 96);
        $listL
                ->setListSource($dataSourceS)
                ->setKeyField('sym')
                ->setListField(array('nazwa', 'nazwa_rm'))
                ->setColumnSpace(' - ')
                ->addConditionalRowFormat($cityFilter, 'row-bold')
                ->setAlign(Css::ALIGN_CLIENT)
                ->removeClass(Css::CORNER_ALL)
        ;

        $titleL = new Element\Text('localityTitle');
        $titleL->setAlign(Css::ALIGN_CLIENT)
                ->setValue('Locality')
                ->addClass(Css::TEXT_ALIGN_HORIZONTAL_CENTER);

        $headerL = new Container\Panel('localityHeader');
        $headerL->setHeight(25)
                ->setAlign(Css::ALIGN_TOP)
                ->addElement($titleL)
                ->addClasses(array(Css::WIDGET_HEADER, Css::CORNER_TOP));

        $bodyL = new Container\Panel();
        $bodyL->setAlign(Css::ALIGN_CLIENT)
                ->addElement($listL);
        
        $panelL = new Container\Panel();
        $panelL->addContainer($headerL)
                ->addContainer($bodyL)
                ->setAlign(Css::ALIGN_RIGHT)
                ->setWidth(25, '%')
                ->setSpace()
        ;
        $panelMain->addContainer($panelL);

        $code = new Element\TextFileView('airportFormCode');
        $code
                ->setFileName('../application/forms/Teryt.php')
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $description = new Element\DocFileView('demoDescription');
        $description
                ->setFileName('../application/views/scripts/demo/teryt_description.phtml')
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        //zakładka opisu
        $tabpanel[0] = new Container\TabPane();
        $tabpanel[0]
                ->setTitle('Description')
                ->addElement($description)
        ;

        //zakładka demo
        $tabpanel[1] = new Container\TabPane();
        $tabpanel[1]
                ->setTitle('Demo')
                ->addContainer($panelMain)
        ;

        //zakładka kodu
        $tabpanel[2] = new Container\TabPane();
        $tabpanel[2]
                ->setTitle('Code')
                ->addElement($code)
        ;

        //główny kontener zakładek
        $tab = new Container\Tab('tab1');
        $tab
                ->setAlign(Css::ALIGN_CLIENT)
                ->addContainers($tabpanel)
        ;

        $this->addContainer($tab);
    }

}

