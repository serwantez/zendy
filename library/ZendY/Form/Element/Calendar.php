<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kalendarz
 *
 * @author Piotr Zając
 */
class Calendar extends CustomList {

    use \ZendY\ControlTrait;

    /**
     * Parametry
     */

    const PARAM_CURRENT_DATE = 'currentDate';
    const PARAM_RANGE = 'range';
    /**
     * Zakres wyświetlania dat kalendarza
     */
    const RANGE_WEEK = 'week';
    const RANGE_MONTH = 'month';

    /**
     * Właściwości komponentu
     */
    const PROPERTY_CURRENTDATE = 'currentDate';
    const PROPERTY_DIALOG = 'dialog';
    const PROPERTY_RANGE = 'range';

    /**
     * Tablica właściwości
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_CURRENTDATE,
        self::PROPERTY_DIALOG,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_MULTIOPTIONS,
        self::PROPERTY_NAME,
        self::PROPERTY_RANGE,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Identyfikator okna dialogowego
     * 
     * @var string
     */
    protected $_dialog = null;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'calendar';
        $this->setRegisterInArrayValidator(false);
        $this->addClasses(array(
            Css::CALENDAR,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setRange(self::RANGE_MONTH);
        $this->setCurrentDate(new \Zend_Date());
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setEmptyValue($empty = true) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setColumnSpace($space) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zwraca zakresy wyświetlania dat
     * 
     * @return array
     */
    public static function getRanges() {
        $translator = \Zend_Form::getDefaultTranslator();
        return array(
            self::RANGE_WEEK => $translator->translate('Week'),
            self::RANGE_MONTH => $translator->translate('Month')
        );
    }

    /**
     * Ustawia zakres kalendarza
     * 
     * @param string $range
     * @return \ZendY\Form\Element\Calendar
     */
    public function setRange($range) {
        $this->setJQueryParam(self::PARAM_RANGE, $range);
        return $this;
    }

    /**
     * Zwraca zakres kalendarza
     * 
     * @return string
     */
    public function getRange() {
        return $this->getJQueryParam(self::PARAM_RANGE);
    }

    /**
     * Ustawia bieżącą datę kalendarza
     * (nie musi być datą dnia dzisiejszego)
     * 
     * @param \Zend_Date|string $date
     * @return \ZendY\Form\Element\Calendar
     */
    public function setCurrentDate($date) {
        $this->setJQueryParam(self::PARAM_CURRENT_DATE, $date);
        return $this;
    }

    /**
     * Zwraca bieżącą datę kalendarza
     * 
     * @return \Zend_Date
     */
    public function getCurrentDate() {
        return $this->getJQueryParam(self::PARAM_CURRENT_DATE);
    }

    /**
     * Odświeża zakres kalendarza
     * 
     * @param array $params
     * @return \ZendY\Form\Element\Calendar
     */
    public function refreshPeriod($params) {
        if (isset($params[self::PARAM_CURRENT_DATE]))
            $this->setCurrentDate($params[self::PARAM_CURRENT_DATE]);
        if (isset($params[self::PARAM_RANGE]))
            $this->setRange($params[self::PARAM_RANGE]);
        return $this;
    }

    /**
     * Zwraca daty graniczne dla ustawionego zakresu kalendarza
     * 
     * @return \ZendY\Period
     */
    public function getPeriod() {
        $date[0] = new \Zend_Date($this->getCurrentDate());
        $date[1] = new \Zend_Date($this->getCurrentDate());
        switch ($this->getRange()) {
            case self::RANGE_WEEK:
                $wday = $date[0]->get(\Zend_Date::WEEKDAY_DIGIT);
                $date[0]->addDay(-$wday);
                $date[1]->addDay(6 - $wday);
                break;
            case self::RANGE_MONTH:
            default:
                //początek miesiąca
                $date[0]->setDay(1);
                $wday = $date[0]->get(\Zend_Date::WEEKDAY_DIGIT);
                //najbliższa niedziela wstecz
                $date[0]->addDay(-$wday);
                $date[1]->setDay(1);
                //koniec miesiąca
                $date[1]->addDay($date[1]->get(\Zend_Date::MONTH_DAYS) - 1);
                $wday = $date[1]->get(\Zend_Date::WEEKDAY_DIGIT);
                //najbliższa sobota naprzód
                $date[1]->addDay(6 - $wday);
                break;
        }
        return new \ZendY\Date\Period($date[0], $date[1]);
    }

    /**
     * Ustawia okno dialogowe
     * 
     * @param string $dialog
     * @return \ZendY\Form\Element\Calendar
     */
    public function setDialog($dialog) {
        $this->_dialog = $dialog;
        return $this;
    }

    /**
     * Zwraca okno dialogowe
     * 
     * @return string
     */
    public function getDialog() {
        return $this->_dialog;
    }

}
