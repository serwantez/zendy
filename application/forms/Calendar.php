<?php

namespace Application\Form;

use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\DataSet\App\Calendar as DbCalendar;
use ZendY\Db\Filter;
use ZendY\Db\Form;
use ZendY\Db\Form\Element as DbElement;

/**
 * Application_Form_Calendar
 *
 * Formularz kalendarza
 *
 * @author Piotr Zając
 */
class Calendar extends Form {

    public function init() {
        $this->setAttrib('id', 'calendarForm');
        $this->setAlign(Css::ALIGN_CLIENT);
        $this->setAjaxValidator(false);

        $dataSet = new DbCalendar('calendar');
        $dataSourceCalendar = new DataSource('dataSourceCalendar', $dataSet);

        $localFeastFilter = new Filter();
        $localFeastFilter->addFilter(DbCalendar::COL_WEIGHT_NUMBER, array(4, 8, 11, 14), DataSet::OPERATOR_IN);

        $calendar = new DbElement\Calendar('calendar');
        $calendar->addClass(Css::ALIGN_CLIENT)
                ->setListSource($dataSourceCalendar)
                ->setKeyField(DbCalendar::COL_ID)
                ->setListField(array(DbCalendar::COL_NAME, DbCalendar::COL_WEIGHT_NUMBER))
                ->setDateField(DbCalendar::COL_YEAR_DATE)
                ->setHolidayField(DbCalendar::COL_HOLIDAY)
                ->addConditionalRowFormat($localFeastFilter, 'row-italic row-grey')
        ;

        $this->addElement($calendar);
    }

}

