<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Db\Form;
use ZendY\Form\Container;
use ZendY\Db\Form\Container as DbContainer;
use ZendY\Form\Element;
use ZendY\Form\Element\Grid\Column;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet;
use ZendY\Db\DataSet\App\Country as DbCountry;
use ZendY\Db\Form\Element as DbElement;

class Country extends Form {

    public function init() {
        $this->setEnctype(\Zend_Form::ENCTYPE_MULTIPART);

        $dataSet = new DbCountry(array(
                    'name' => 'country'
                ));
        $dataSources[0] = new DataSource(array(
                    'name' => 'countrySource',
                    'dataSet' => $dataSet
                ));

        $dataSetCur = new DataSet\Table(array(
                    'name' => 'currency'
                ));
        $dataSetCur->setTableName('currency')
                ->sortAction(array('field' => 'name'))
        ;

        $listSources['currency'] = new DataSource(array(
                    'name' => 'currencySource',
                    'dataSet' => $dataSetCur
                ));


        //przyciski akcji
        $actions = array(
            DataSet\Base::ACTION_REFRESH,
            DataSet\Base::ACTION_EXPORTEXCEL,
            DataSet\Base::ACTION_PRINT
        );

        $btnAdd = new DbElement\Button('addButton');
        $btnAdd
                ->setDataSource($dataSources[0])
                ->setDataAction(DataSet\Editable::ACTION_ADD)
        ;

        $btnEdit = new DbElement\Button('editButton');
        $btnEdit
                ->setDataSource($dataSources[0])
                ->setDataAction(DataSet\Editable::ACTION_EDIT)
                ->setShortKey('F3')
        ;

        $btnOpenFilter = new Element\IconButton('btnOpenFilter');
        $btnOpenFilter
                ->setLabel('Filter')
                ->setShortKey('Ctrl+F')
                ->setIcons(Css::ICON_SEARCH)
        ;


        $grid = new DbElement\Grid();
        $grid
                ->setListSource($dataSources[0])
                ->setKeyField(DbCountry::COL_ID)
                ->addColumn(new Column(
                                array(
                                    'name' => DbCountry::COL_ID,
                                    'label' => 'ID',
                                    'width' => 35,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCountry::COL_NAME,
                                    'label' => 'Name',
                                    'width' => 220
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCountry::COL_NAME_PL,
                                    'label' => 'Polish name',
                                    'width' => 220
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCountry::COL_ALFA2,
                                    'label' => 'Alfa2',
                                    'width' => 50
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCountry::COL_ALFA3,
                                    'label' => 'Alfa3',
                                    'width' => 50
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCountry::COL_CURRENCY_NAME,
                                    'label' => 'Currency',
                                    'width' => 150
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCountry::COL_UIC,
                                    'label' => 'UIC',
                                    'width' => 50
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCountry::COL_LINK_WIKI,
                                    'label' => 'Wikipedia',
                                    'width' => 260,
                                    'decorators' => array(
                                        array('Link', array('link' => '', 'target' => '_blank'))
                                    )
                        )))
                ->setAlign(Css::ALIGN_CLIENT)
                ->setPager(30)
                ->setSorter()
                ->setJQueryParam(
                        Element\Grid::PARAM_EVENT_DBLCLICKROW
                        , sprintf('$("#%s").trigger("click");', $btnEdit->getId())
                )
        ;

        $panel1 = new Container\Panel();
        $panel1->addElement($grid)
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace()
        ;
        $this->addContainer($panel1);


        $i = 0;
        $elements[$i] = new DbElement\Edit('country_id');
        $elements[$i]
                ->setLabel('ID')
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_ID)
                ->setRequired(true)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('name');
        $elements[$i]
                ->setLabel('Name')
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_NAME)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('name_pl');
        $elements[$i]
                ->setLabel('Polish name')
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_NAME_PL)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('alfa2');
        $elements[$i]
                ->setLabel('ALFA2 code')
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_ALFA2)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('alfa3');
        $elements[$i]
                ->setLabel('ALFA3 code')
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_ALFA3)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('uic');
        $elements[$i]
                ->setLabel('UIC code')
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_UIC)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Image('flag');
        $elements[$i]
                ->setLabel('Flag')
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_FLAG)
                ->setFit(true)
                ->setWidth(150)
                ->setHeight(80)
        ;

        $i++;
        $elements[$i] = new DbElement\Combobox('waluta');
        $elements[$i]
                ->setLabel('Currency')
                ->setDataSource($dataSources[0])
                ->setDataField('currency')
                ->setListSource($listSources['currency'])
                ->setKeyField('id')
                ->setListField(array('name', 'iso'))
                ->setStaticRender()
        ;


        $i++;
        $elements[$i] = new DbElement\SpinEdit();
        $elements[$i]
                ->setLabel('Population')
                ->setDataSource($dataSources[0])
                ->setDataField('population')
                ->setStep(0.1)
                ->setMin(0)
                ->setMax(10000000)
        ;

        $i++;
        $elements[$i] = new DbElement\Textarea();
        $elements[$i]
                ->setLabel('Description')
                ->setDataSource($dataSources[0])
                ->setDataField('description')
                ->setWidth(250)
                ->setHeight(60)
        ;

        $i++;
        $elements[$i] = new DbElement\Url();
        $elements[$i]
                ->setLabel('Wikipedia')
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_LINK_WIKI)
                ->setWidth(250)
        ;

        $detailsPanel = new Container\Panel();
        $detailsPanel->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
                ->addClass(Css::SCROLL_AUTO)
        ;

        $nav = new DbContainer\Navigator();
        $nav->setActions($actions)
                ->setDataSource($dataSources[0])
                ->addElement($btnOpenFilter)
                ->addElement($btnAdd)
                ->addElement($btnEdit)
                ->setSpace(array('value' => 0.2, 'unit' => 'em'))
        ;

        $this->addContainer($nav);

        $dialog = new DbContainer\EditDialog(array(
                    'name' => 'countryDetails'
                ));
        $openEvent = sprintf('$("#%s").trigger("focus");'
                , $elements[0]->getName()
        );

        $dialog
                ->setDataSource($dataSources[0])
                ->setTitle('Country details')
                ->setWidth(500)
                ->setHeight(530)
                ->addContainer($detailsPanel)
                ->addOpener($btnEdit)
                ->addOpener($btnAdd)
                ->setJQueryParam(
                        Container\Dialog::PARAM_EVENT_OPEN
                        , $openEvent
                )
        ;
        $this->addContainer($dialog);


        // okno filtrowania
        $dialogCountryFilter = new DbContainer\FilterDialog('dialogCountryFilter');
        $panelCountryFilters = new Container\Panel('panelCountryFilters');
        $filterElements[0] = new DbElement\Filter\IconEdit('filterCountry');
        $filterElements[0]
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_NAME_PL)
                ->setLabel('Polish name')
        ;

        $filterElements[1] = new DbElement\Filter\IconEdit('filterCurrency');
        $filterElements[1]
                ->setDataSource($dataSources[0])
                ->setDataField(DbCountry::COL_CURRENCY_NAME)
                ->setLabel('Currency')
        ;

        $panelCountryFilters
                ->addElements($filterElements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        $dialogCountryFilter
                ->setDataSource($dataSources[0])
                ->setTitle('Country filter')
                ->setWidth(350)
                ->setHeight(250)
                ->addContainer($panelCountryFilters)
                ->addOpener($btnOpenFilter)
        ;
        $this->addContainer($dialogCountryFilter);
    }

}

