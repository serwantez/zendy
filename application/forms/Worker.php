<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\Form;
use ZendY\Form\Element;
use ZendY\Form\Container;
use ZendY\Db\Form\Container as DbContainer;
use ZendY\Db\DataSet;
use ZendY\Db\Form\Element as DbElement;
use Application\Model;

/**
 * Formularz demonstracyjny z rejestrem pracowników
 *
 * @author Piotr Zając
 */
class Worker extends Form {

    public function init() {
        //ustawienia ogólne
        $this->setAttrib('id', 'demoForm');
        $this->setAlign(Css::ALIGN_CLIENT);
        $this->setAjaxValidator(false);

        //zbiory i źródła danych
        $dataSet = new Model\Worker('worker');
        $dataSourceWorker = new DataSource('dataSourceWorker', $dataSet);

        $dataSet = new Model\Sex('workerSex');
        $dataSourceSex = new DataSource('dataSourceSex', $dataSet);

        $dataSet = new Model\Agreement('workerAgreement');
        $dataSet->setMasterSource($dataSourceWorker)
                ->setMasterField(Model\Worker::COL_ID)
                ->setIndexField('ag.' . Model\Agreement::COL_WORKER_ID)
        ;
        $dataSourceAgreement = new DataSource('dataSourceAgreement', $dataSet);

        $dataSet = new DataSet\App\ListItem('agreementType');
        $dataSet->setList(16);
        $dataSourceAgreementType = new DataSource('dataSourceAgreementType', $dataSet);

        $dataSet = new DataSet\App\Country('workerCountry');
        $dataSourceCountry = new DataSource('dataSourceCountry', $dataSet);

        //własne przyciski akcji
        $btnAdd = new DbElement\Button('addButton');
        $btnAdd
                ->setDataSource($dataSourceWorker)
                ->setDataAction(DataSet\Editable::ACTION_ADD)
                ->setShortKey('Ctrl+N')
        ;

        $btnOpenDetails = new Element\IconButton('btnOpenDetails');
        $btnOpenDetails
                ->setLabel('Open details')
                ->setShortKey('F3')
                ->setIcons(Css::ICON_PENCIL)
        ;

        $btnOpenFilter = new Element\IconButton('btnOpenFilter');
        $btnOpenFilter
                ->setLabel('Filter')
                ->setShortKey('Ctrl+F')
                ->setIcons(Css::ICON_SEARCH)
        ;

        $btnOpenAgreement = new Element\IconButton('btnOpenAgreement');
        $btnOpenAgreement
                ->setLabel('Agreements')
                ->setVisibleText(true)
        ;

        //główny panel z gridem
        $gridWorker = new DbElement\Grid('gridWorker');
        $gridWorker
                ->setListSource($dataSourceWorker)
                ->setKeyField(Model\Worker::COL_ID)
                ->addColumn(new Element\Grid\Column(
                                Model\Worker::COL_ID
                                , array(
                            'label' => 'ID',
                            'width' => 45,
                            'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Worker::COL_SURNAME
                                , array(
                            'label' => 'Surname',
                            'width' => 180
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Worker::COL_FIRSTNAME
                                , array(
                            'label' => 'Firstname',
                            'width' => 180
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Worker::COL_COUNTRY_NAME
                                , array(
                            'label' => 'Country',
                            'width' => 160
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Worker::COL_ADDRESS
                                , array(
                            'label' => 'Address',
                            'width' => 220
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Worker::COL_POSTAL_CODE
                                , array(
                            'label' => 'Postal code',
                            'width' => 70
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Worker::COL_POST
                                , array(
                            'label' => 'Postal locality',
                            'width' => 170
                        )))
                ->setAlign(Css::ALIGN_CLIENT)
                ->setPager(30)
                ->setSorter()
                ->setJQueryParam(
                        Element\Grid::PARAM_EVENT_DBLCLICKROW
                        , sprintf('$("#%s").trigger("click");', $btnOpenDetails->getId())
                )
        ;
        $panelMain = new Container\Panel();
        $panelMain
                ->addElement($gridWorker)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        //dolny panel nawigatora z przyciskami akcji
        $actions = array(
            DataSet\Base::ACTION_REFRESH,
            DataSet\Base::ACTION_EXPORTEXCEL,
            array('action' => DataSet\Base::ACTION_PRINT, 'shortkey' => 'Ctrl+P')
        );

        $nav = new DbContainer\Navigator();
        $nav
                ->setActions($actions)
                ->setDataSource($dataSourceWorker)
                ->addElement($btnOpenFilter)
                ->addElement($btnAdd)
                ->addElement($btnOpenDetails)
                ->addElement($btnOpenAgreement)
                ->setAlign(Css::ALIGN_BOTTOM)
        ;

        //okno szczegółów i edycji
        $i = 0;
        $elements[$i] = new DbElement\Text('worker_id');
        $elements[$i]
                ->setLabel('ID')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_ID)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('firstname');
        $elements[$i]
                ->setLabel('Firstname')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_FIRSTNAME)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('surname');
        $elements[$i]
                ->setLabel('Surname')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_SURNAME)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\RadioButton('sex');
        $elements[$i]
                ->setLabel('Sex')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_SEX)
                ->setListSource($dataSourceSex)
                ->setKeyField('id')
                ->setListField('name')
                ->setStaticRender()
        ;

        $i++;
        $elements[$i] = new DbElement\Combobox('country');
        $elements[$i]
                ->setLabel('Country')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_COUNTRY_ID)
                ->setListSource($dataSourceCountry)
                ->setKeyField(DataSet\App\Country::COL_ID)
                ->setListField(DataSet\App\Country::COL_NAME)
                ->setStaticRender(true)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('address');
        $elements[$i]
                ->setLabel('Address')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_ADDRESS)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('postalcode');
        $elements[$i]
                ->setLabel('Postal code')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_POSTAL_CODE)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('post');
        $elements[$i]
                ->setLabel('Postal locality')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_POST)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Image('photo');
        $elements[$i]
                ->setLabel('Photo')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_PHOTO)
                ->setFit(true)
                ->setWidth(80)
                ->setHeight(100)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('phone');
        $elements[$i]
                ->setLabel('Phone')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_PHONE)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Email('email');
        $elements[$i]
                ->setLabel('Email')
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_EMAIL)
                ->setWidth(250)
        ;

        $detailsPanel = new Container\Panel();
        $detailsPanel->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
                ->addClass(Css::SCROLL_AUTO)
        ;

        $dialog = new DbContainer\EditDialog('countryDetails');
        $dialog
                ->setDataSource($dataSourceWorker)
                ->setTitle('Worker details')
                ->setWidth(480)
                ->setHeight(490)
                ->addContainer($detailsPanel)
                ->addOpener($btnOpenDetails)
                ->addOpener($btnAdd)
        ;

        //okno filtrowania
        $dialogWorkerFilter = new DbContainer\FilterDialog('dialogWorkerFilter');
        $panelWorkerFilters = new Container\Panel('panelWorkerFilters');

        $filterElements[0] = new DbElement\Filter\IconEdit('filterSurname');
        $filterElements[0]
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_SURNAME)
                ->setLabel('Surname')
        ;

        $filterElements[1] = new DbElement\Filter\IconEdit('filterFirstname');
        $filterElements[1]
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_FIRSTNAME)
                ->setLabel('Firstname')
        ;

        $filterElements[2] = new DbElement\Filter\IconEdit('filterCountry');
        $filterElements[2]
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_COUNTRY_NAME)
                ->setLabel('Country')
        ;

        $filterElements[3] = new DbElement\Filter\IconEdit('filterAddress');
        $filterElements[3]
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_ADDRESS)
                ->setLabel('Address')
        ;

        $filterElements[4] = new DbElement\Filter\IconEdit('filterPostalCode');
        $filterElements[4]
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_POSTAL_CODE)
                ->setLabel('Postal code')
        ;

        $filterElements[5] = new DbElement\Filter\IconEdit('filterPost');
        $filterElements[5]
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_POST)
                ->setLabel('Postal locality')
        ;

        $panelWorkerFilters
                ->addElements($filterElements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        $dialogWorkerFilter
                ->setDataSource($dataSourceWorker)
                ->setTitle('Worker filter')
                ->setWidth(350)
                ->setHeight(250)
                ->addContainer($panelWorkerFilters)
                ->addOpener($btnOpenFilter)
        ;

        //okno z umowami pracownika
        $gridAgreement = new DbElement\Grid('gridAgreement');
        $gridAgreement
                ->setListSource($dataSourceAgreement)
                ->setKeyField(Model\Agreement::COL_ID)
                ->addColumn(new Element\Grid\Column(
                                Model\Agreement::COL_ID
                                , array(
                            'label' => 'ID',
                            'width' => 45,
                            'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Agreement::COL_AGREEMENT_TYPE_NAME
                                , array(
                            'label' => 'Agreement type',
                            'width' => 180
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Agreement::COL_JOB_TIME
                                , array(
                            'label' => 'Job time',
                            'width' => 50
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Agreement::COL_DATE_SIGNING
                                , array(
                            'label' => 'Signing date',
                            'width' => 70
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Agreement::COL_DATE_START
                                , array(
                            'label' => 'Start date',
                            'width' => 70
                        )))
                ->addColumn(new Element\Grid\Column(
                                Model\Agreement::COL_DATE_END
                                , array(
                            'label' => 'End date',
                            'width' => 70
                        )))
                ->setAlign(Css::ALIGN_CLIENT)
                ->setPager(30)
                ->setSorter()
        ;
        $panelGridAgreement = new Container\Panel();
        $panelGridAgreement
                ->addElement($gridAgreement)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $agreementElement[0] = new DbElement\Text('workerFirstname');
        $agreementElement[0]
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_FIRSTNAME)
                ->setLabel('Firstname', 100)
        ;

        $agreementElement[1] = new DbElement\Text('workerSurname');
        $agreementElement[1]
                ->setDataSource($dataSourceWorker)
                ->setDataField(Model\Worker::COL_SURNAME)
                ->setLabel('Surname', 100)
        ;

        $agreementElement[2] = new DbElement\Combobox('agreementType');
        $agreementElement[2]
                ->setListSource($dataSourceAgreementType)
                ->setListField(DataSet\App\ListItem::COL_NAME)
                ->setKeyField(DataSet\App\ListItem::COL_ITEM_ID)
                ->setStaticRender(true)
                ->setDataSource($dataSourceAgreement)
                ->setDataField(Model\Agreement::COL_AGREEMENT_TYPE_ID)
                ->setLabel('Agreement type', 100)
                ->setWidth(120)
        ;

        $agreementElement[3] = new DbElement\SpinEdit('jobTime');
        $agreementElement[3]
                ->setDataSource($dataSourceAgreement)
                ->setDataField(Model\Agreement::COL_JOB_TIME)
                ->setLabel('Job time [h]', 100)
                ->setWidth(120)
        ;

        $agreementElement[4] = new DbElement\DatePicker('dateSigning');
        $agreementElement[4]
                ->setDataSource($dataSourceAgreement)
                ->setDataField(Model\Agreement::COL_DATE_SIGNING)
                ->setLabel('Signing date', 100)
                ->setWidth(120)
        ;

        $agreementElement[5] = new DbElement\DatePicker('dateStart');
        $agreementElement[5]
                ->setDataSource($dataSourceAgreement)
                ->setDataField(Model\Agreement::COL_DATE_START)
                ->setLabel('Start date', 100)
                ->setWidth(120)
        ;

        $agreementElement[6] = new DbElement\DatePicker('dateEnd');
        $agreementElement[6]
                ->setDataSource($dataSourceAgreement)
                ->setDataField(Model\Agreement::COL_DATE_END)
                ->setLabel('End date', 100)
                ->setWidth(120)
        ;

        $panelAgreementDetails = new Container\Panel();
        $panelAgreementDetails
                ->addElements($agreementElement)
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(250)
        ;

        $dialogAgreement = new DbContainer\EditDialog('dialogAgreement');
        $dialogAgreement
                ->setDataSource($dataSourceAgreement)
                ->setTitle('Worker agreements')
                ->setWidth(800)
                ->setHeight(500)
                ->addContainer($panelGridAgreement)
                ->addContainer($panelAgreementDetails)
                ->addOpener($btnOpenAgreement)
        ;

        $code = new Element\TextFileView('workerFormCode');
        $code
                ->setFileName('../application/forms/Worker.php')
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $description = new Element\DocFileView('demoDescription');
        $description
                ->setFileName('../application/views/scripts/demo/worker_description.phtml')
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
                ->addContainer($nav)
                ->addContainer($dialog)
                ->addContainer($dialogWorkerFilter)
                ->addContainer($dialogAgreement)
        ;

        //zakładka kodu
        $tabpanel[2] = new Container\TabPane();
        $tabpanel[2]
                ->setTitle('Code')
                ->addElement($code);

        //główny kontener zakładek
        $tab = new Container\Tab();
        $tab
                ->setAlign(Css::ALIGN_CLIENT)
                ->addContainers($tabpanel)
        ;

        $this->addContainer($tab);
    }

}

