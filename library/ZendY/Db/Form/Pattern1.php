<?php

//ZBIORY DANYCH

$dataSet_umodel_ = new Model\_umodel_(array(
            'name' => '_model_'
        ));
$dataSource_umodel_ = new DataSource(array(
            'name' => 'dataSource_umodel_',
            'dataSet' => $dataSet_umodel_
        ));

//PREZENTACJA

$grid_umodel_ = new DbElement\Grid(array(
            'name' => 'grid_umodel_',
            'align' => Css::ALIGN_CLIENT,
            'sorter' => true,
            'pager' => 20,
            'listSource' => $dataSource_umodel_,
            'keyField' => $dataSet_umodel_->getPrimary(),
            'columns' => array(
                _columns_
            ),
        ));

$panelGrid = new Container\Panel(array(
            'name' => 'panelGrid',
            'align' => Css::ALIGN_CLIENT,
            'space' => 2,
            'elements' => array($grid_umodel_)
        ));


//NAWIGACJA

$btnAdd_umodel_ = new DbElement\Button('btnAdd_umodel_');
$btnAdd_umodel_
        ->setDataSource($dataSource_umodel_)
        ->setDataAction(DataSet\Editable::ACTION_ADD)
        ->setVisibleText(true)
        ->setShortKey('Ctrl+N')
;

$btnEdit_umodel_ = new Element\Button('btnEdit_umodel_');
$btnEdit_umodel_
        ->setLabel('Edit')
        ->setShortKey('F3')
;

$btnFilter_umodel_ = new Element\Button('btnFilter_umodel_');
$btnFilter_umodel_
        ->setLabel('Filter')
        ->setShortKey('Ctrl+F')
;


$nav_umodel_ = new DbContainer\Navigator(array(
            'name' => '_model_Nav',
            'dataSource' => $dataSource_umodel_,
            'actions' => array(
                DataSet\Base::ACTION_FIRST,
                DataSet\Base::ACTION_PREVIOUS,
                DataSet\Base::ACTION_NEXT,
                DataSet\Base::ACTION_LAST,
                DataSet\Base::ACTION_REFRESH,
                DataSet\Base::ACTION_PRINT,
                DataSet\Base::ACTION_EXPORTEXCEL,
            ),
            'space' => array('value' => 0.2, 'unit' => 'em'),
            'elements' => array(
                $btnAdd_umodel_,
                $btnEdit_umodel_,
                $btnFilter_umodel_
            ),
        ));

//EDYCJA

_editControlsDef_;

$panelEdit_umodel_ = new Container\Panel(array(
            'name' => 'panelEdit_umodel_',
            'align' => Css::ALIGN_CLIENT,
            'elements' => array(
                _editControls_
            )
        ));

$dialogEdit_umodel_ = new DbContainer\EditDialog(array(
            'name' => 'dialogEdit_umodel_',
            'actions' => array(
                array('action' => DataSet\Editable::ACTION_SAVE, 'text' => true, 'shortkey' => 'Ctrl+S'),
                array('action' => DataSet\Editable::ACTION_CANCEL, 'text' => true),
                array('action' => DataSet\Editable::ACTION_DELETE, 'text' => true),
            ),
            'width' => 450,
            'height' => 500,
            'dataSource' => $dataSource_umodel_,
            'openers' => array($btnAdd_umodel_, $btnEdit_umodel_),
            'containers' => array($panelEdit_umodel_)
        ));


//FILTROWANIE

_filterControlsDef_;

$panelFilter_umodel_ = new Container\Panel(array(
            'name' => 'panelFilter_umodel_',
            'align' => Css::ALIGN_CLIENT,
            'elements' => array(
                _filterControls_
            ),
        ));

$dialogFilter_umodel_ = new DbContainer\FilterDialog(array(
            'name' => 'dialogFilter_umodel_',
            'width' => 450,
            'height' => 500,
            'dataSource' => $dataSource_umodel_,
            'openers' => array($btnFilter_umodel_),
            'containers' => array($panelFilter_umodel_)
        ));



$this->setContainers(array(
    $panelGrid,
    $nav_umodel_,
    $dialogEdit_umodel_,
    $dialogFilter_umodel_
));
