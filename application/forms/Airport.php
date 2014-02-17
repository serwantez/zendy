<?php

namespace Application\Form;

use ZendY\Css,
    ZendY\Db\DataSource,
    ZendY\Db\DataSet,
    ZendY\Form\Container,
    ZendY\Db\Form\Container as DbContainer,
    ZendY\Form\Element,
    ZendY\Db\Form\Element as DbElement,
    Application\Model;

/**
 * Formularz demonstracyjny z rejestrem portów lotniczych
 *
 * @author Piotr Zając
 */
class Airport extends \ZendY\Db\Form {

    public function init() {
        //zbiory i źródła danych
        $dataSetAirport = new Model\Airport(array(
                    Model\Airport::PROPERTY_NAME => 'airport'
                ));
        $dataSourceAirport = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'airportSource',
                    DataSource::PROPERTY_DATASET => $dataSetAirport
                ));

        $dataSetCountry = new DataSet\App\Country(array(
                    DataSet\App\Country::PROPERTY_NAME => 'country'
                ));
        $dataSourceCountry = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'countrySource',
                    DataSource::PROPERTY_DATASET => $dataSetCountry
                ));

        //własne przyciski akcji
        $btnOpenFilter = new Element\IconButton(array(
                    Element\IconButton::PROPERTY_NAME => 'btnOpenFilter',
                    Element\IconButton::PROPERTY_LABEL => 'Filter',
                    Element\IconButton::PROPERTY_SHORTKEY => 'Ctrl+F',
                    Element\IconButton::PROPERTY_ICONS => Css::ICON_SEARCH
                ));

        //lista lotnisk
        $listAirports = new DbElement\Listbox(array(
                    DbElement\Listbox::PROPERTY_NAME => 'listAirports',
                    DbElement\Listbox::PROPERTY_LISTSOURCE => $dataSourceAirport,
                    DbElement\Listbox::PROPERTY_KEYFIELD => Model\Airport::COL_ID,
                    DbElement\Listbox::PROPERTY_LISTFIELD => Model\Airport::COL_NAME,
                    DbElement\Listbox::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                ));
        //ustawienie wybranych dekoratorów
        $listAirports
                ->setDecorators(array(
                    array('UiWidgetMultiElement'),
                    array('HtmlTag', array('class' => Css::ALIGN_CLIENT))
                ))
        ;

        $panelLeft = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelLeft',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_LEFT,
                    Container\Panel::PROPERTY_WIDTH => 300,
                    Container\Panel::PROPERTY_SPACE => 2
                ));
        $panelLeft->setElements(array($listAirports));

        $editCode = new DbElement\Edit(array(
                    DbElement\Edit::PROPERTY_NAME => 'code',
                    DbElement\Edit::PROPERTY_LABEL => 'IATA code',
                    DbElement\Edit::PROPERTY_WIDTH => 50,
                    DbElement\Edit::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbElement\Edit::PROPERTY_DATAFIELD => Model\Airport::COL_CODE
                ));

        $editName = new DbElement\Edit(array(
                    DbElement\Edit::PROPERTY_NAME => 'name',
                    DbElement\Edit::PROPERTY_LABEL => 'Name',
                    DbElement\Edit::PROPERTY_WIDTH => 250,
                    DbElement\Edit::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbElement\Edit::PROPERTY_DATAFIELD => Model\Airport::COL_NAME
                ));

        $comboboxCountry = new DbElement\Combobox(array(
                    DbElement\Combobox::PROPERTY_NAME => 'country_id',
                    DbElement\Combobox::PROPERTY_LABEL => 'Country',
                    DbElement\Combobox::PROPERTY_WIDTH => 250,
                    DbElement\Combobox::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbElement\Combobox::PROPERTY_DATAFIELD => Model\Airport::COL_COUNTRY_ID,
                    DbElement\Combobox::PROPERTY_LISTSOURCE => $dataSourceCountry,
                    DbElement\Combobox::PROPERTY_LISTFIELD => DataSet\App\Country::COL_NAME,
                    DbElement\Combobox::PROPERTY_KEYFIELD => DataSet\App\Country::COL_ID,
                    DbElement\Combobox::PROPERTY_STATICRENDER => true
                ));

        $panelTop = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelTop',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_TOP,
                    Container\Panel::PROPERTY_CLASSES => array(Css::WIDGET_CONTENT),
                    Container\Panel::PROPERTY_HEIGHT => 100
                ));
        $panelTop->setElements(array($editCode, $editName, $comboboxCountry));

        $map = new DbElement\PointMap(array(
                    DbElement\PointMap::PROPERTY_NAME => 'coordinates',
                    DbElement\PointMap::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbElement\PointMap::PROPERTY_DATAFIELD => Model\Airport::COL_COORDINATES,
                    DbElement\PointMap::PROPERTY_ZOOM => 14,
                    DbElement\PointMap::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                    DbElement\PointMap::PROPERTY_LABEL => 'Coordinates',
                ));

        $panelMap = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelMap',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_CLIENT
                ));
        $panelMap->setElements(array($map));

        $panelMain = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelMain',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_CLIENT,
                    Container\Panel::PROPERTY_SPACE => 2
                ));
        $panelMain->setContainers(array($panelTop, $panelMap));

        $nav = new DbContainer\Navigator(array(
                    DbContainer\Navigator::PROPERTY_NAME => 'nav',
                    DbContainer\Navigator::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbContainer\Navigator::PROPERTY_ACTIONS => array(
                        DataSet\Base::ACTION_FIRST,
                        DataSet\Base::ACTION_PREVIOUS,
                        DataSet\Base::ACTION_NEXT,
                        DataSet\Base::ACTION_LAST,
                        DataSet\Base::ACTION_REFRESH,
                        DataSet\Base::ACTION_EXPORTEXCEL,
                        DataSet\Editable::ACTION_ADD,
                        DataSet\Editable::ACTION_EDIT,
                        DataSet\Editable::ACTION_SAVE
                    ),
                    DbContainer\Navigator::PROPERTY_SPACE => array('value' => 0.2, 'unit' => 'em')
                ));
        $nav->setElements(array($btnOpenFilter));

        //okno filtrowania
        $filterName = new DbElement\Filter\IconEdit(array(
                    DbElement\Filter\IconEdit::PROPERTY_NAME => 'filterName',
                    DbElement\Filter\IconEdit::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbElement\Filter\IconEdit::PROPERTY_DATAFIELD => Model\Airport::COL_NAME,
                    DbElement\Filter\IconEdit::PROPERTY_LABEL => 'Name'
                ));

        $filterCode = new DbElement\Filter\IconEdit(array(
                    DbElement\Filter\IconEdit::PROPERTY_NAME => 'filterCode',
                    DbElement\Filter\IconEdit::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbElement\Filter\IconEdit::PROPERTY_DATAFIELD => Model\Airport::COL_CODE,
                    DbElement\Filter\IconEdit::PROPERTY_LABEL => 'IATA code'
                ));

        $filterCountry = new DbElement\Filter\IconEdit(array(
                    DbElement\Filter\IconEdit::PROPERTY_NAME => 'filterCountry',
                    DbElement\Filter\IconEdit::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbElement\Filter\IconEdit::PROPERTY_DATAFIELD => Model\Airport::COL_COUNTRY_NAME,
                    DbElement\Filter\IconEdit::PROPERTY_LABEL => 'Country'
                ));

        $panelAirportFilter = new Container\Panel(array(
                    Container\Panel::PROPERTY_NAME => 'panelAirportFilter',
                    Container\Panel::PROPERTY_ALIGN => Css::ALIGN_CLIENT
                ));
        $panelAirportFilter->setElements(array($filterName, $filterCode, $filterCountry));

        $dialogAirportFilter = new DbContainer\FilterDialog(array(
                    DbContainer\FilterDialog::PROPERTY_NAME => 'dialogAirportFilter',
                    DbContainer\FilterDialog::PROPERTY_DATASOURCE => $dataSourceAirport,
                    DbContainer\FilterDialog::PROPERTY_TITLE => 'Airport filter',
                    DbContainer\FilterDialog::PROPERTY_WIDTH => 350,
                    DbContainer\FilterDialog::PROPERTY_HEIGHT => 200,
                    DbContainer\FilterDialog::PROPERTY_OPENERS => array($btnOpenFilter)
                ));
        $dialogAirportFilter->setContainers(array($panelAirportFilter));

        $code = new Element\TextFileView(array(
                    Element\TextFileView::PROPERTY_NAME => 'airportFormCode',
                    Element\TextFileView::PROPERTY_FILENAME => '../application/forms/Airport.php',
                    Element\TextFileView::PROPERTY_ALIGN => Css::ALIGN_CLIENT
                ));

        $description = new Element\DocFileView(array(
                    Element\DocFileView::PROPERTY_NAME => 'demoDescription',
                    Element\DocFileView::PROPERTY_FILENAME => '../application/views/scripts/demo/airport_description.phtml',
                    Element\DocFileView::PROPERTY_ALIGN => Css::ALIGN_CLIENT
                ));

        //zakładka opisu
        $tabPanel0 = new Container\TabPane(array(
                    Container\TabPane::PROPERTY_NAME => 'tabPanel0',
                    Container\TabPane::PROPERTY_TITLE => 'Description'
                ));
        $tabPanel0->setElements(array($description));

        //zakładka demo
        $tabPanel1 = new Container\TabPane(array(
                    Container\TabPane::PROPERTY_NAME => 'tabPanel1',
                    Container\TabPane::PROPERTY_TITLE => 'Demo'
                ));
        $tabPanel1->setContainers(array(
            $panelLeft,
            $panelMain,
            $nav,
            $dialogAirportFilter
        ));

        //zakładka kodu
        $tabPanel2 = new Container\TabPane(array(
                    Container\TabPane::PROPERTY_NAME => 'tabPanel2',
                    Container\TabPane::PROPERTY_TITLE => 'Code'
                ));
        $tabPanel2->setElements(array($code));

        //główny kontener zakładek
        $tab = new Container\Tab(array(
                    Container\Tab::PROPERTY_NAME => 'tab',
                    Container\Tab::PROPERTY_ALIGN => Css::ALIGN_CLIENT
                ));
        $tab->setContainers(array(
            $tabPanel0,
            $tabPanel1,
            $tabPanel2
        ));

        $this->setContainers(array($tab));
    }

}

