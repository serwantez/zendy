<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataSource;

/**
 * Bazodanowa kontrolka kalendarza z wydarzeniami
 *
 * @author Piotr Zając
 */
class EventCalendar extends Calendar {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_EVENT_DATEFIELD = 'eventDateField';
    const PROPERTY_EVENT_DESCRIPTIONFIELD = 'eventDescriptionField';
    const PROPERTY_EVENT_DIALOG = 'eventDialog';
    const PROPERTY_EVENT_KEYFIELD = 'eventKeyField';
    const PROPERTY_EVENT_TIMEFIELD = 'eventField';
    const PROPERTY_EVENT_TYPEFIELD = 'eventTypeField';
    const PROPERTY_EVENT_SOURCE = 'eventSource';

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
        self::PROPERTY_DATAFIELD,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_DATEFIELD,
        self::PROPERTY_DIALOG,
        self::PROPERTY_DISABLED,
        self::PROPERTY_EVENT_DATEFIELD,
        self::PROPERTY_EVENT_DESCRIPTIONFIELD,
        self::PROPERTY_EVENT_DIALOG,
        self::PROPERTY_EVENT_KEYFIELD,
        self::PROPERTY_EVENT_SOURCE,
        self::PROPERTY_EVENT_TIMEFIELD,
        self::PROPERTY_EVENT_TYPEFIELD,
        self::PROPERTY_HOLIDAYFIELD,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_KEYFIELD,
        self::PROPERTY_LABEL,
        self::PROPERTY_LISTFIELD,
        self::PROPERTY_LISTSOURCE,
        self::PROPERTY_NAME,
        self::PROPERTY_RANGE,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_STATICRENDER,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Identyfikator okna dialogowego dla wydarzeń
     * 
     * @var string
     */
    protected $_eventDialog = null;

    /**
     * Zwraca tablicę parametrów nawigacyjnych przekazywanych do przeglądarki
     * 
     * @param string $list
     * @return array
     */
    public function getFrontNaviParams($list = 'standard') {
        if ($list == 'event') {
            $this->setFrontNaviParam('eventKeyField', $this->getEventKeyField());
            $this->setFrontNaviParam('eventField', $this->getEventField());
            $this->setFrontNaviParam('eventDialog', $this->getEventDialog());
        }
        return parent::getFrontNaviParams($list);
    }

    /**
     * Ustawia pole etykiety wydarzenia
     * 
     * @param string|array $name
     * @return \ZendY\Db\Form\Element\EventCalendar
     */
    public function setEventField($name) {
        if (!is_array($name))
            $name = array($name);
        $this->_lists['event']['listField'] = $name;
        return $this;
    }

    /**
     * Zwraca pole etykiety wydarzenia
     * 
     * @return array
     */
    public function getEventField() {
        return $this->_lists['event']['listField'];
    }

    /**
     * Ustawia pole klucza ze zbioru wydarzeń
     * 
     * @param string|array $name
     * @return \ZendY\Db\Form\Element\EventCalendar
     */
    public function setEventKeyField($name) {
        if (!is_array($name))
            $name = array($name);
        $this->_lists['event']['keyField'] = $name;
        return $this;
    }

    /**
     * Zwraca pole klucza ze zbioru wydarzeń
     * 
     * @return array
     */
    public function getEventKeyField() {
        return $this->_lists['event']['keyField'];
    }

    /**
     * Ustawia pole daty w zbiorze zdarzeń
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\Calendar
     */
    public function setEventDateField($name) {
        $this->_lists['event']['listField']['date'] = $name;
        return $this;
    }

    /**
     * Zwraca pole daty zdarzenia
     * 
     * @return string
     */
    public function getEventDateField() {
        return $this->_lists['event']['listField']['date'];
    }

    /**
     * Ustawia pole opisu zdarzenia
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\Calendar
     */
    public function setEventDescriptionField($name) {
        $this->_lists['event']['listField']['description'] = $name;
        return $this;
    }

    /**
     * Zwraca pole opisu zdarzenia
     * 
     * @return string
     */
    public function getEventDescriptionField() {
        return $this->_lists['event']['listField']['description'];
    }

    /**
     * Ustawia pole godziny zdarzenia
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\Calendar
     */
    public function setEventTimeField($name) {
        $this->_lists['event']['listField']['time'] = $name;
        return $this;
    }

    /**
     * Zwraca pole godziny zdarzenia
     * 
     * @return string
     */
    public function getEventTimeField() {
        return $this->_lists['event']['listField']['time'];
    }

    /**
     * Ustawia pole typu zdarzenia
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\Calendar
     */
    public function setEventTypeField($name) {
        $this->_lists['event']['listField']['type'] = $name;
        return $this;
    }

    /**
     * Zwraca pole typu zdarzenia
     * 
     * @return string
     */
    public function getEventTypeField() {
        return $this->_lists['event']['listField']['type'];
    }

    /**
     * Ustawia źródło wydarzeń
     * 
     * @param \ZendY\Db\DataSource|null $eventSource 
     * @return \ZendY\Db\Form\Element\EventCalendar
     */
    public function setEventSource(&$eventSource) {
        if ($eventSource instanceof DataSource) {
            $eventSource->addNaviControl($this, 'event');
            $this->_lists['event']['listSource'] = $eventSource;
        }
        return $this;
    }

    /**
     * Zwraca źródło wydarzeń
     * 
     * @return \ZendY\Db\DataSource 
     */
    public function getEventSource() {
        return $this->_lists['event']['listSource'];
    }

    /**
     * Czy jest ustawione źródło wydarzeń
     * 
     * @return bool
     */
    public function hasEventSource() {
        if (isset($this->_lists['event']['listSource']))
            return true;
        else
            return false;
    }

    /**
     * Zwraca pola ze zbioru danych listy potrzebne do wyrenderowania kontrolki
     * 
     * @param string $list
     * @return array
     */
    public function getFields($list = 'standard') {
        if ($list == 'standard') {
            return array_unique(array_merge(
                                    $this->getKeyField()
                                    , array(
                                $this->getDateField(),
                                $this->getHolidayField()
                                    ), $this->getListField()
                            ));
        } elseif ($list == 'event') {
            return array_unique(array_merge(
                                    $this->getEventKeyField()
                                    , $this->getEventField()
                            ));
        } else
            return array();
    }

    /**
     * Ustawia okno dialogowe dla wydarzeń
     * 
     * @param string $dialog
     * @return \ZendY\Db\Form\Element\EventCalendar
     */
    public function setEventDialog($dialog) {
        $this->_eventDialog = $dialog;
        return $this;
    }

    /**
     * Zwraca okno dialogowe dla wydarzeń
     * 
     * @return string
     */
    public function getEventDialog() {
        return $this->_eventDialog;
    }

    /**
     * Renderowanie kontrolki
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasEventSource()) {
            $this->getEventSource()->getDataSet()->setPeriod($this->getPeriod());
        }
        return parent::render($view);
    }

}
