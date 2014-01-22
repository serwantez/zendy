<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet;
use ZendY\Form\Container;
use ZendY\Db\Form\Container as DbContainer;
use ZendY\Form\Element;
use ZendY\Db\Form\Element as DbElement;
use Application\Model;

/**
 * Formularz demonstracyjny z rejestrem portów lotniczych
 *
 * @author Piotr Zając
 */
class Airport extends \ZendY\Db\Form {

    public function init() {
        //ustawienia ogólne
        $this->setAttrib('id', 'airportForm');
        $this->setAjaxValidator(false);

        //zbiory i źródła danych
        $dataSet = new Model\Airport('airport');
        $dataSourceAirport = new DataSource('airportSource', $dataSet);

        $dataSet = new DataSet\App\Country('country');
        $dataSourceCountry = new DataSource('countrySource', $dataSet);

        //własne przyciski akcji
        $btnOpenFilter = new Element\IconButton('btnOpenFilter');
        $btnOpenFilter
                ->setLabel('Filter')
                ->setShortKey('Ctrl+F')
                ->setIcons(Css::ICON_SEARCH)
        ;

        //lista lotnisk
        $listElement[0] = new DbElement\Listbox();
        $listElement[0]
                ->setListSource($dataSourceAirport)
                ->setKeyField(Model\Airport::COL_ID)
                ->setListField(Model\Airport::COL_NAME)
                ->setDecorators(array(
                    array('UiWidgetMultiElement'),
                    array('HtmlTag', array('class' => Css::ALIGN_CLIENT))
                ))
                ->setAlign(Css::ALIGN_CLIENT)

        ;

        $panelLeft = new Container\Panel();
        $panelLeft->addElements($listElement)
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(300)
                ->setSpace()
        ;

        $i = 0;
        $elements[$i] = new DbElement\Edit('code');
        $elements[$i]
                ->setLabel('IATA code')
                ->setDataSource($dataSourceAirport)
                ->setDataField(Model\Airport::COL_CODE)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('name');
        $elements[$i]
                ->setLabel('Name')
                ->setDataSource($dataSourceAirport)
                ->setDataField(Model\Airport::COL_NAME)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Combobox('country_id');
        $elements[$i]
                ->setLabel('Country')
                ->setListSource($dataSourceCountry)
                ->setKeyField(DataSet\App\Country::COL_ID)
                ->setListField(DataSet\App\Country::COL_NAME)
                ->setDataSource($dataSourceAirport)
                ->setDataField(Model\Airport::COL_COUNTRY_ID)
                ->setWidth(200)
                ->setStaticRender()
        ;


        $panelMain = new Container\Panel();
        $panelMain
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace()
        ;

        $panelTop = new Container\Panel();
        $panelTop->addElements($elements)
                ->setAlign(Css::ALIGN_TOP)
                ->addClass(Css::WIDGET_CONTENT)
                ->setHeight(100)
        ;
        $panelMain->addContainer($panelTop);

        $map = new DbElement\PointMap('coordinates');
        $map
                ->setDataSource($dataSourceAirport)
                ->setDataField(Model\Airport::COL_COORDINATES)
                ->setZoom(14)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $panelMap = new Container\Panel();
        $panelMap->addElement($map)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        $panelMain->addContainer($panelMap);

        $actions = array(
            DataSet\Base::ACTION_FIRST,
            DataSet\Base::ACTION_PREVIOUS,
            DataSet\Base::ACTION_NEXT,
            DataSet\Base::ACTION_LAST,
            DataSet\Base::ACTION_REFRESH,
            DataSet\Base::ACTION_EXPORTEXCEL,
            DataSet\Editable::ACTION_ADD,
            DataSet\Editable::ACTION_EDIT,
            DataSet\Editable::ACTION_SAVE,
            DataSet\Editable::ACTION_DELETE
        );
        $nav = new DbContainer\Navigator();
        $nav
                ->setDataSource($dataSourceAirport)
                ->setActions($actions)
                ->addElement($btnOpenFilter)
                ->setSpace();

        //okno filtrowania
        $dialogAirportFilter = new DbContainer\FilterDialog('dialogAirportFilter');
        $panelAirportFilter = new Container\Panel('panelAirportFilter');

        $filterElements[0] = new DbElement\Filter\IconEdit('filterName');
        $filterElements[0]
                ->setDataSource($dataSourceAirport)
                ->setDataField(Model\Airport::COL_NAME)
                ->setLabel('Name')
        ;

        $filterElements[1] = new DbElement\Filter\IconEdit('filterCode');
        $filterElements[1]
                ->setDataSource($dataSourceAirport)
                ->setDataField(Model\Airport::COL_CODE)
                ->setLabel('IATA code')
        ;

        $filterElements[2] = new DbElement\Filter\IconEdit('filterCountry');
        $filterElements[2]
                ->setDataSource($dataSourceAirport)
                ->setDataField(Model\Airport::COL_COUNTRY_NAME)
                ->setLabel('Country')
        ;

        $panelAirportFilter
                ->addElements($filterElements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        $dialogAirportFilter
                ->setDataSource($dataSourceAirport)
                ->setTitle('Airport filter')
                ->setWidth(350)
                ->setHeight(200)
                ->addContainer($panelAirportFilter)
                ->addOpener($btnOpenFilter)
        ;

        $code = new Element\TextFileView('airportFormCode');
        $code
                ->setFileName('../application/forms/Airport.php')
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $description = new Element\DocFileView('demoDescription');
        $description
                ->setFileName('../application/views/scripts/demo/airport_description.phtml')
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
                ->addContainer($panelLeft)
                ->addContainer($panelMain)
                ->addContainer($nav)
                ->addContainer($dialogAirportFilter)
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

