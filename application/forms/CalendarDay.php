<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container\Panel;
use ZendY\Form\Container\Dialog;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Db\Form\Container\FilterDialog;
use ZendY\Db\Form\Container\EditDialog;
use ZendY\Css;
use ZendY\Form\Element\Grid\Column;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\DataSet\App\ListItem;
use ZendY\Db\DataSet\App\CalendarDay as DbCalendarDay;
use ZendY\Db\DataSet\App\Entity;
use ZendY\Db\DataSet\App\EntityCalendar;
use ZendY\Db\DataSet\Editable;
use ZendY\Db\DataSet\ArraySet;
use ZendY\Db\Filter;
use ZendY\Form\Element;
use ZendY\Db\Form\Element as DbElement;

/**
 * Formularz obchodów kalendarzowych
 *
 * @author Piotr Zając
 */
class CalendarDay extends Form {

    public function init() {
        $this->setEnctype(\Zend_Form::ENCTYPE_MULTIPART);

        /*
         * Źródła danych
         */
        $dataSet = new DbCalendarDay(array(
                    'name' => 'calendar'
                ));
        $dataSet->sortAction(array('field' => DbCalendarDay::COL_DAY));
        $dataSet->sortAction(array('field' => DbCalendarDay::COL_WEIGHT_NUMBER));
        $dataSource['calendar'] = new DataSource(array(
                    'name' => 'calendarSource',
                    'dataSet' => $dataSet
                ));

        /* $y = 2013;
          print_r(DbCalendarDay::getOrdinarySundays($y));
          exit; */

        $dataSet = new ListItem(array(
                    'name' => 'weightType',
                    'list' => 10
                ));
        $dataSource['weight_type'] = new DataSource(array(
                    'name' => 'weightTypeSource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new ListItem(array(
                    'name' => 'weightNumber',
                    'list' => 14
                ));
        $dataSource['weight_number'] = new DataSource(array(
                    'name' => 'weightNumberSource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new ListItem(array(
                    'name' => 'movability',
                    'list' => 11
                ));
        $dataSource['movability'] = new DataSource(array(
                    'name' => 'movabilitySource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new ArraySet(array(
                    'name' => 'holiday'
                ));
        $dataSet->setData(array(
                    array('id' => 0, 'flag' => 'no'),
                    array('id' => 1, 'flag' => 'yes')
                ))
                ->setPrimary('id');
        $dataSource['holiday'] = new DataSource(array(
                    'name' => 'holidaySource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new ListItem(array(
                    'name' => 'dependencyFunction',
                    'list' => 12
                ));
        $dataSource['dependency_function'] = new DataSource(array(
                    'name' => 'dependencyFunctionSource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new Entity(array(
                    'name' => 'entity'
                ));
        $dataSource['entity'] = new DataSource(array(
                    'name' => 'entitySource',
                    'dataSet' => $dataSet
                ));

        $dataSet = new EntityCalendar(array(
                    'name' => 'entityCalendarFunction'
                ));
        $dataSet->addMaster($dataSource['calendar']
                , DbCalendarDay::COL_ID
                , EntityCalendar::COL_CALENDAR_ID)
        ;
        $dataSource['entity_calendar'] = new DataSource(array(
                    'name' => 'entityCalendarSource',
                    'dataSet' => $dataSet
                ));


        /*
         * Przyciski
         */

        $btnAdd = new DbElement\Button('addButton');
        $btnAdd
                ->setDataSource($dataSource['calendar'])
                ->setDataAction(Editable::ACTION_ADD)
                ->setShortKey('Ctrl+N')
                ->setVisibleText(TRUE)
        ;

        $btnAddCopy = new DbElement\Button('btnAddCopy');
        $btnAddCopy
                ->setDataSource($dataSource['calendar'])
                ->setDataAction(Editable::ACTION_ADDCOPY)
                //->setShortKey('Ctrl+C')
                ->setVisibleText(TRUE)
        ;

        $btnEdit = new DbElement\Button('editButton');
        $btnEdit
                ->setDataSource($dataSource['calendar'])
                ->setDataAction(Editable::ACTION_EDIT)
                ->setShortKey('F3')
                ->setVisibleText(TRUE)
        ;

        $btnOpenEntities = new Element\IconButton('btnOpenEntities');
        $btnOpenEntities
                ->setLabel('Entities')
                ->setIcons(Css::ICON_HOME)
                ->setShortKey('F6')
                ->setVisibleText(TRUE)
        ;

        $btnOpenFilter = new Element\IconButton('btnOpenFilter');
        $btnOpenFilter
                ->setLabel('Filter')
                ->setShortKey('Ctrl+F')
                ->setIcons(Css::ICON_SEARCH)
        ;

        $btnSave = new DbElement\Button('saveButton');
        $btnSave->setDataSource($dataSource['calendar'])
                ->setDataAction(Editable::ACTION_SAVE)
                ->setShortKey('Ctrl+S')
                ->setVisibleText(TRUE)
        ;

        $btnDel = new DbElement\Button('deleteButton');
        $btnDel->setDataSource($dataSource['calendar'])
                ->setDataAction(Editable::ACTION_DELETE)
                ->setVisibleText(TRUE)
        ;

        $btnCan = new DbElement\Button('cancelButton');
        $btnCan->setDataSource($dataSource['calendar'])
                ->setDataAction(Editable::ACTION_CANCEL)
                ->setShortKey('Esc')
                ->setVisibleText(TRUE)
        ;



        /*
         * Pozostałe elementy
         */
        $icons = array(0 => Css::ICON_CANCEL, 1 => Css::ICON_CHECK);
        $grid = new DbElement\Grid('gridCalendar');
        $localFeastFilter = new Filter();
        $localFeastFilter->addFilter(DbCalendarDay::COL_WEIGHT_NUMBER, array(4, 8, 11, 14), DataSet::OPERATOR_IN);
        $grid
                ->setListSource($dataSource['calendar'])
                ->setKeyField(DbCalendarDay::COL_ID)
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_ID,
                                    'label' => 'ID',
                                    'width' => 35,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_NAME,
                                    'label' => 'Polish name',
                                    'width' => 270
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_WEIGHT_TYPE_NAME,
                                    'label' => 'Weight type',
                                    'width' => 180
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_WEIGHT_NUMBER,
                                    'label' => 'Weight no.',
                                    'width' => 70,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_MOVABILITY_NAME,
                                    'label' => 'Movability',
                                    'width' => 120,
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_DAY,
                                    'label' => 'Date',
                                    'width' => 100,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_CENTER
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_DEPENDENCY_FUNCTION_NAME,
                                    'label' => 'Dep.function',
                                    'width' => 150
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_DEPENDENCY_PARAM,
                                    'label' => 'Dep.param',
                                    'width' => 80,
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_RIGHT
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => DbCalendarDay::COL_HOLIDAY,
                                    'label' => 'Holiday',
                                    'width' => 50,
                                    'decorators' => array(
                                        array('Icon', array('icons' => $icons))
                                    ),
                                    'align' => Css::TEXT_ALIGN_HORIZONTAL_CENTER
                        )))
                ->setALign(Css::ALIGN_CLIENT)
                ->setPager(30)
                ->setSorter()
                ->addConditionalRowFormat($localFeastFilter, 'row-italic row-grey')
                ->setJQueryParam(
                        Element\Grid::PARAM_EVENT_DBLCLICKROW
                        , sprintf('$("#%s") . trigger("click");
        ', $btnEdit->getId())
                )
        ;

        $panel1 = new Panel();
        $panel1->addElement($grid)
                ->setAlign(Css::ALIGN_CLIENT)
                ->setSpace()
        ;
        $this->addContainer($panel1);


        $i = 0;
        $elements[$i] = new DbElement\Text('id');
        $elements[$i]
                ->setLabel('ID')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_ID)
                ->setWidth(50)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('name');
        $elements[$i]
                ->setLabel('Name')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_NAME)
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Combobox('weight_type');
        $elements[$i]
                ->setLabel('Weight type')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_WEIGHT_TYPE)
                ->setListSource($dataSource['weight_type'])
                ->setKeyField(ListItem::COL_ITEM_ID)
                ->setListField(ListItem::COL_NAME)
                ->setStaticRender()
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Combobox('weight_number');
        $elements[$i]
                ->setLabel('Weight number')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_WEIGHT_NUMBER)
                ->setListSource($dataSource['weight_number'])
                ->setKeyField(ListItem::COL_ITEM_ID)
                ->setListField(ListItem::COL_NAME)
                ->setStaticRender()
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Radio('movability');
        $elements[$i]
                ->setLabel('Movability')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_MOVABILITY)
                ->setListSource($dataSource['movability'])
                ->setKeyField(ListItem::COL_ITEM_ID)
                ->setListField(ListItem::COL_NAME)
                ->setStaticRender()
        ;

        $i++;
        $elements[$i] = new DbElement\DatePicker('day');
        $elements[$i]
                ->setLabel('Date')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_DAY)
                ->setWidth(100)
                ->removeValidator('date')
        ;

        $i++;
        $elements[$i] = new DbElement\Combobox('dependency_function');
        $elements[$i]
                ->setLabel('Dependency function')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_DEPENDENCY_FUNCTION)
                ->setListSource($dataSource['dependency_function'])
                ->setKeyField(ListItem::COL_ITEM_ID)
                ->setListField(ListItem::COL_NAME)
                ->setStaticRender()
                ->setEmptyValue()
                ->setWidth(250)
        ;

        $i++;
        $elements[$i] = new DbElement\Edit('dependency_param');
        $elements[$i]
                ->setLabel('Dependency param')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_DEPENDENCY_PARAM)
                ->setWidth(100)
        ;

        $i++;
        $elements[$i] = new DbElement\Radio('holiday');
        $elements[$i]
                ->setLabel('Holiday')
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_HOLIDAY)
                ->setListSource($dataSource['holiday'])
                ->setKeyField('id')
                ->setListField('flag')
                ->setStaticRender()
        ;

        $detailsPanel = new Panel();
        $detailsPanel->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
                ->addClass(Css::SCROLL_AUTO)
        ;

        $actions = array(
            DataSet::ACTION_FIRST,
            DataSet::ACTION_PREVIOUS,
            DataSet::ACTION_NEXT,
            DataSet::ACTION_LAST,
            DataSet::ACTION_REFRESH,
            DataSet::ACTION_EXPORTEXCEL,
            DataSet::ACTION_PRINT
        );

        $nav = new Navigator();
        $nav->setActions($actions)
                ->setDataSource($dataSource['calendar'])
                ->addElements(array(
                    $btnOpenFilter,
                    $btnAdd,
                    $btnAddCopy,
                    $btnEdit,
                    $btnOpenEntities))
                ->setSpace(array('value' => 0.2, 'unit' => 'em'))
        ;

        $this->addContainer($nav);

        $dialogCalendar = new EditDialog(array(
                    'name' => 'calendarDetails'
                ));
        $dialogCalendar
                ->setDataSource($dataSource['calendar'])
                ->setTitle('Calendar day details')
                ->setWidth(480)
                ->setHeight(490)
                ->addContainer($detailsPanel)
                ->addOpener($btnEdit)
                ->addOpener($btnAdd)
                ->addOpener($btnAddCopy)
        ;
        $this->addContainer($dialogCalendar);

        $editDescription = new DbElement\Edit('editDescription');
        $editDescription->setDataSource($dataSource['entity_calendar'])
                ->setDataField(EntityCalendar::COL_DESCRIPTION)
                ->setLabel('Feast description', 110)
        ;

        $panelDescription = new Panel('panelDescription');
        $panelDescription->addElement($editDescription)
                ->setHeight(35)
                ->setAlign(Css::ALIGN_TOP)
        ;

        $treeviewEntity = new DbElement\Treeview('treeviewEntity');
        $treeviewEntity
                ->setListSource($dataSource['entity'])
                ->setKeyField(Entity::COL_ID)
                ->setListField(array(Entity::COL_NAME))
                //->setStaticRender()
                ->setDataSource($dataSource['entity_calendar'])
                ->setDataField(EntityCalendar::COL_ENTITY_ID)
                ->setDecorators(array(
                    array('UiWidgetMultiElement'),
                    array('HtmlTag', array('class' => Css::ALIGN_CLIENT))
                ))
                ->addClass(Css::ALIGN_CLIENT)
        ;

        $panelTreeview = new Panel('panelTreeview');
        $panelTreeview->addElement($treeviewEntity)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        $gridEntity = new DbElement\Grid('gridEntity');
        $gridEntity
                ->setAlign(Css::ALIGN_CLIENT)
                ->setListSource($dataSource['entity_calendar'])
                ->setKeyField(EntityCalendar::COL_ID)
                ->addColumn(new Column(
                                array(
                                    'name' => EntityCalendar::COL_ENTITY_NAME,
                                    'label' => 'Entity name',
                                    'width' => 235,
                        )))
                ->addColumn(new Column(
                                array(
                                    'name' => EntityCalendar::COL_DESCRIPTION,
                                    'label' => 'Feast description',
                                    'width' => 235,
                        )))
        ;

        $panelEntityCalendarTreeview = new Panel(array(
                    'name' => 'panelEntityCalendarTreeview'
                ));
        $panelEntityCalendarTreeview
                ->addContainer($panelDescription)
                ->addContainer($panelTreeview)
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(310)
                ->addClass(Css::SCROLL_DISABLE)
                ->setSpace()
        ;

        $panelEntityCalendarGrid = new Panel(array(
                    'name' => 'panelEntityCalendarGrid'
                ));
        $panelEntityCalendarGrid
                ->addElement($gridEntity)
                ->setAlign(Css::ALIGN_CLIENT)
                ->addClass(Css::SCROLL_DISABLE)
                ->setSpace()
        ;

        $panelEntityCalendarDetails = new Panel(array(
                    'name' => 'panelEntityCalendarDetails'
                ));
        $panelEntityCalendarDetails
                ->addContainer($panelEntityCalendarTreeview)
                ->addContainer($panelEntityCalendarGrid)
                ->setAlign(Css::ALIGN_CLIENT)
                ->addClass(Css::SCROLL_AUTO)
        ;

        $btnAddEntity = new DbElement\Button('btnAddEntity');
        $btnAddEntity
                ->setDataSource($dataSource['entity_calendar'])
                ->setDataAction(EntityCalendar::ACTION_ADD)
                ->setVisibleText(TRUE)
        ;

        $btnEditEntity = new DbElement\Button('btnEditEntity');
        $btnEditEntity
                ->setDataSource($dataSource['entity_calendar'])
                ->setDataAction(EntityCalendar::ACTION_EDIT)
                ->setVisibleText(TRUE)
        ;

        $btnSaveEntity = new DbElement\Button('btnSaveEntity');
        $btnSaveEntity
                ->setDataSource($dataSource['entity_calendar'])
                ->setDataAction(EntityCalendar::ACTION_SAVE)
                ->setVisibleText(TRUE)
        ;

        $btnDelEntity = new DbElement\Button('btnDelEntity');
        $btnDelEntity
                ->setDataSource($dataSource['entity_calendar'])
                ->setDataAction(Editable::ACTION_DELETE)
                ->setVisibleText(TRUE)
        ;

        $btnCancelEntity = new DbElement\Button('btnCancelEntities');
        $btnCancelEntity
                ->setDataSource($dataSource['entity_calendar'])
                ->setDataAction(Editable::ACTION_CANCEL)
                ->setVisibleText(TRUE)
        ;

        $btnCloseEntity = new Element\IconButton('btnCloseEntity');
        $btnCloseEntity
                ->setLabel('Close')
                ->setIcons(Css::ICON_CLOSE)
                ->setVisibleText(TRUE)
        ;

        $panelEntityCalendarNav = new Navigator(array(
                    'name' => 'panelEntityCalendarNav'
                ));
        $panelEntityCalendarNav
                ->setDataSource($dataSource['entity_calendar'])
                ->setAlign(Css::ALIGN_BOTTOM)
                ->addElement($btnAddEntity)
                ->addElement($btnEditEntity)
                ->addElement($btnSaveEntity)
                ->addElement($btnDelEntity)
                ->addElement($btnCancelEntity)
                ->addElement($btnCloseEntity)
                ->setSpace(array('value' => 0.2, 'unit' => 'em'))
        ;

        $dialogEntityCalendar = new Dialog(array(
                    'name' => 'dialogEntityCalendar',
                ));
        $dialogEntityCalendar
                ->setTitle('Entities')
                ->setModal(true)
                ->setWidth(720)
                ->setHeight(500)
                ->addContainer($panelEntityCalendarDetails)
                ->addContainer($panelEntityCalendarNav)
                ->addOpener($btnOpenEntities)
                ->addCloser($btnCancelEntity)
                ->addCloser($btnCloseEntity)
                ->setJQueryParam(Dialog::PARAM_RESIZABLE, false)
                ->setJQueryParam(Dialog::PARAM_CLOSEONESCAPE, false)
        ;
        $this->addContainer($dialogEntityCalendar);

        // okno filtrowania
        $dialogCalendarFilter = new FilterDialog(array(
                    'name' => 'dialogCalendarFilter'
                ));
        $panelCalendarFilters = new Panel(array(
                    'name' => 'panelCalendarFilters'
                ));
        $filterElements[0] = new DbElement\Filter\IconEdit('filterFeastName');
        $filterElements[0]
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_NAME)
                ->setOperator(DataSet::OPERATOR_CONTAIN)
                ->setLabel('Name')
        ;

        $filterElements[1] = new DbElement\Filter\IconEdit('filterWeightType');
        $filterElements[1]
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_WEIGHT_TYPE_NAME)
                ->setLabel('Weight type')
        ;

        $filterElements[2] = new DbElement\Filter\IconEdit('filterWeightNo');
        $filterElements[2]
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_WEIGHT_NUMBER)
                ->setLabel('Weight no.')
        ;

        $filterElements[3] = new DbElement\Filter\IconEdit('filterMovability');
        $filterElements[3]
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_MOVABILITY_NAME)
                ->setLabel('Movability')
        ;

        $filterElements[4] = new DbElement\Filter\IconEdit('filterDate');
        $filterElements[4]
                ->setDataSource($dataSource['calendar'])
                ->setDataField(DbCalendarDay::COL_DAY)
                ->setLabel('Day')
        ;

        $panelCalendarFilters
                ->addElements($filterElements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        $dialogCalendarFilter
                ->setDataSource($dataSource['calendar'])
                ->setTitle('Calendar filter')
                ->setWidth(350)
                ->setHeight(250)
                ->addContainer($panelCalendarFilters)
                ->addOpener($btnOpenFilter)
        ;
        $this->addContainer($dialogCalendarFilter);
    }

}
