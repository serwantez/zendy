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
        //zbiory i źródła danych
        $dataSetW = new Model\Worker(array(
                    Model\Worker::PROPERTY_NAME => 'worker'
                ));
        $dataSourceWorker = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'dataSourceWorker',
                    DataSource::PROPERTY_DATASET => $dataSetW
                ));

        $dataSetS = new Model\Sex(array(
                    Model\Sex::PROPERTY_NAME => 'workerSex'
                ));
        $dataSourceSex = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'dataSourceSex',
                    DataSource::PROPERTY_DATASET => $dataSetS
                ));

        $dataSetA = new Model\Agreement(array(
                    Model\Agreement::PROPERTY_NAME => 'workerAgreement',
                    Model\Agreement::PROPERTY_MASTER => array(
                        array(
                            'masterSource' => $dataSourceWorker,
                            'masterField' => Model\Worker::COL_ID,
                            'detailField' => 'ag.' . Model\Agreement::COL_WORKER_ID
                        )
                    )
                ));
        $dataSourceAgreement = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'dataSourceAgreement',
                    DataSource::PROPERTY_DATASET => $dataSetA
                ));

        $dataSetL = new DataSet\App\ListItem(array(
                    DataSet\App\ListItem::PROPERTY_NAME => 'agreementType',
                    DataSet\App\ListItem::PROPERTY_LIST => 16
                ));
        $dataSourceAgreementType = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'dataSourceAgreementType',
                    DataSource::PROPERTY_DATASET => $dataSetL
                ));

        $dataSetC = new DataSet\App\Country(array(
                    DataSet\App\Country::PROPERTY_NAME => 'workerCountry'
                ));
        $dataSourceCountry = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'dataSourceCountry',
                    DataSource::PROPERTY_DATASET => $dataSetC
                ));

        //własne przyciski akcji
        $btnAdd = new DbElement\Button(array(
                    DbElement\Button::PROPERTY_NAME => 'addButton',
                    DbElement\Button::PROPERTY_SHORTKEY => 'Ctrl+N',
                    DbElement\Button::PROPERTY_DATASOURCE => $dataSourceWorker,
                    DbElement\Button::PROPERTY_DATAACTION => DataSet\Editable::ACTION_ADD,
                ));

        $btnOpenDetails = new Element\IconButton(array(
                    DbElement\Button::PROPERTY_NAME => 'btnOpenDetails',
                    DbElement\Button::PROPERTY_LABEL => 'Open details',
                    DbElement\Button::PROPERTY_SHORTKEY => 'F3',
                    DbElement\Button::PROPERTY_ICONS => array(Css::ICON_PENCIL)
                ));

        $btnOpenFilter = new Element\IconButton(array(
                    Element\IconButton::PROPERTY_NAME => 'btnOpenFilter',
                    Element\IconButton::PROPERTY_LABEL => 'Filter',
                    Element\IconButton::PROPERTY_SHORTKEY => 'Ctrl+F',
                    Element\IconButton::PROPERTY_ICONS => Css::ICON_SEARCH
                ));

        $btnOpenAgreement = new Element\IconButton(array(
                    Element\IconButton::PROPERTY_NAME => 'btnOpenAgreement',
                    Element\IconButton::PROPERTY_LABEL => 'Agreements',
                    Element\IconButton::PROPERTY_VISIBLETEXT => true
                ));

        //główny panel z gridem
        $gridWorker = new DbElement\Grid(array(
                    DbElement\Grid::PROPERTY_NAME => 'gridWorker',
                    DbElement\Grid::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                    DbElement\Grid::PROPERTY_LISTSOURCE => $dataSourceWorker,
                    DbElement\Grid::PROPERTY_KEYFIELD => Model\Worker::COL_ID,
                    DbElement\Grid::PROPERTY_PAGER => 30,
                    DbElement\Grid::PROPERTY_SORTER => true,
                    DbElement\Grid::PROPERTY_COLUMNS => array(
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Worker::COL_ID,
                                    'label' => 'ID',
                                    'width' => 45,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                                )
                        ),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Worker::COL_SURNAME,
                                    'label' => 'Surname',
                                    'width' => 180
                                )
                        ),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Worker::COL_FIRSTNAME,
                                    'label' => 'Firstname',
                                    'width' => 180
                                )
                        ),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Worker::COL_COUNTRY_NAME,
                                    'label' => 'Country',
                                    'width' => 160
                                )
                        ),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Worker::COL_ADDRESS,
                                    'label' => 'Address',
                                    'width' => 220
                                )
                        ),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Worker::COL_POSTAL_CODE,
                                    'label' => 'Postal code',
                                    'width' => 70
                                )
                        ),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Worker::COL_POST,
                                    'label' => 'Postal locality',
                                    'width' => 170
                                )
                        )
                    ),
                ));
        $gridWorker
                ->setJQueryParam(
                        Element\Grid::PARAM_EVENT_DBLCLICKROW
                        , sprintf('$("#%s").trigger("click");', $btnOpenDetails->getName())
                )
        ;
        $panelMain = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelMain',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                    Container\Panel::PROPERTY_SPACE => 2,
                ));
        $panelMain->setElements(array($gridWorker));

        //dolny panel nawigatora z przyciskami akcji
        $actions = array(
            DataSet\Base::ACTION_REFRESH,
            DataSet\Base::ACTION_EXPORTEXCEL,
            array('action' => DataSet\Base::ACTION_PRINT, 'shortkey' => 'Ctrl+P')
        );

        $nav = new DbContainer\Navigator(array(
                    DbContainer\Navigator::PROPERTY_NAME => 'nav',
                    DbContainer\Navigator::PROPERTY_ACTIONS => $actions,
                    DbContainer\Navigator::PROPERTY_DATASOURCE => $dataSourceWorker,
                    DbContainer\Navigator::PROPERTY_ALIGN => Css::ALIGN_BOTTOM,
                    DbContainer\Navigator::PROPERTY_SPACE => array('value' => 0.2, 'unit' => 'em'),
                ));
        $nav->setElements(array(
            $btnOpenFilter,
            $btnAdd,
            $btnOpenDetails,
            $btnOpenAgreement
        ));

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
                ->setWidth(250)
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

        $detailsPanel = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelDetails'
                ));
        $detailsPanel->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
                ->addClass(Css::SCROLL_AUTO)
        ;

        $dialog = new DbContainer\EditDialog(array(
                    Container\Panel::PROPERTY_NAME => 'countryDetails'
                ));
        $dialog
                ->setDataSource($dataSourceWorker)
                ->setTitle('Worker details')
                ->setWidth(520)
                ->setHeight(520)
                ->addContainer($detailsPanel)
                ->addOpener($btnOpenDetails)
                ->addOpener($btnAdd)
        ;

        //okno filtrowania
        $dialogWorkerFilter = new DbContainer\FilterDialog(array(
                    DbContainer\FilterDialog::PROPERTY_NAME => 'dialogWorkerFilter'
                ));
        $panelWorkerFilters = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelWorkerFilters'
                ));

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
                ->setHeight(330)
                ->addContainer($panelWorkerFilters)
                ->addOpener($btnOpenFilter)
        ;

        //okno z umowami pracownika
        $gridAgreement = new DbElement\Grid(array(
                    DbElement\Grid::PROPERTY_NAME => 'gridAgreement',
                    DbElement\Grid::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                    DbElement\Grid::PROPERTY_LISTSOURCE => $dataSourceAgreement,
                    DbElement\Grid::PROPERTY_KEYFIELD => Model\Agreement::COL_ID,
                    DbElement\Grid::PROPERTY_PAGER => 30,
                    DbElement\Grid::PROPERTY_SORTER => true,
                    DbElement\Grid::PROPERTY_COLUMNS => array(
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Agreement::COL_ID,
                                    'label' => 'ID',
                                    'width' => 45,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Agreement::COL_AGREEMENT_TYPE_NAME,
                                    'label' => 'Agreement type',
                                    'width' => 180
                        )),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Agreement::COL_JOB_TIME,
                                    'label' => 'Job time',
                                    'width' => 50
                        )),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Agreement::COL_DATE_SIGNING,
                                    'label' => 'Signing date',
                                    'width' => 90
                        )),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Agreement::COL_DATE_START,
                                    'label' => 'Start date',
                                    'width' => 90
                        )),
                        new Element\Grid\Column(
                                array(
                                    'name' => Model\Agreement::COL_DATE_END,
                                    'label' => 'End date',
                                    'width' => 90
                        ))
                    ),
                ));
        $panelGridAgreement = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelGridAgreement'
                ));
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

        $panelAgreementDetails = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelAgreementDetails'
                ));
        $panelAgreementDetails
                ->addElements($agreementElement)
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(250)
        ;

        $dialogAgreement = new DbContainer\EditDialog(array(
                    DbContainer\EditDialog::PROPERTY_NAME => 'dialogAgreement'
                ));
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
        $tabPanel0 = new Container\TabPane();
        $tabPanel0
                ->setTitle('Description')
                ->addElement($description)
        ;

        //zakładka demo
        $tabPanel1 = new Container\TabPane();
        $tabPanel1
                ->setTitle('Demo')
                ->addContainer($panelMain)
                ->addContainer($nav)
                ->addContainer($dialog)
                ->addContainer($dialogWorkerFilter)
                ->addContainer($dialogAgreement)
        ;

        //zakładka kodu
        $tabPanel2 = new Container\TabPane();
        $tabPanel2
                ->setTitle('Code')
                ->addElement($code);

        //główny kontener zakładek
        $tab = new Container\Tab(array(
                    Container\Tab::PROPERTY_NAME => 'tab',
                    Container\Tab::PROPERTY_ALIGN => Css::ALIGN_CLIENT
                ));
        $tab->setContainers(array($tabPanel0, $tabPanel1, $tabPanel2));

        $this->setContainers(array($tab));
    }

}

