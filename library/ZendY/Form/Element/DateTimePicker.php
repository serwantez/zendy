<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka daty i czasu
 *
 * @author Piotr Zając
 */
class DateTimePicker extends DatePicker {

    const PARAM_TIMEFORMAT = 'timeFormat';

    /**
     * Właściwości komponentu
     */
    const PROPERTY_TIMEFORMAT = 'timeFormat';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DATEFORMAT,
        self::PROPERTY_DISABLED,
        self::PROPERTY_DURATION,
        self::PROPERTY_LABEL,
        self::PROPERTY_ICON,
        self::PROPERTY_ICON_POSITION,
        self::PROPERTY_INLINE,
        self::PROPERTY_LOCALE,
        self::PROPERTY_MAXDATE,
        self::PROPERTY_MAXLENGTH,
        self::PROPERTY_MINDATE,
        self::PROPERTY_PLACEHOLDER,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TIMEFORMAT,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'dateTimePicker';
        $this->setTimeFormat('HH:mm:ss');
    }

    /**
     * Ustawia format czasu wyświetlanego w kontrolce
     * 
     * @param string $timeFormat
     * @return \ZendY\Form\Element\DateTimePicker
     */
    public function setTimeFormat($timeFormat) {
        $this->setJQueryParam(self::PARAM_TIMEFORMAT, $timeFormat);
        $this->removeValidator('date');
        $this->addValidator(new \Zend_Validate_Date(array('format' => $this->getDateFormat() . ' ' . $timeFormat)), true);
        return $this;
    }

    /**
     * Zwraca format czasu wyświetlanego w kontrolce
     * 
     * @return string
     */
    public function getTimeFormat() {
        return $this->getJQueryParam(self::PARAM_TIMEFORMAT);
    }

}
