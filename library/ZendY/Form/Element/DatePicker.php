<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka daty
 *
 * @author Piotr Zając
 */
class DatePicker extends IconEdit {
    /**
     * Parametry
     */

    const PARAM_DURATION = 'duration';
    const PARAM_LOCALE = 'regional';
    const PARAM_DATEFORMAT = 'dateFormat';
    const PARAM_MINDATE = 'minDate';
    const PARAM_MAXDATE = 'maxDate';

    /**
     * Prędkości włączania kalendarza
     */
    const DURATION_SLOW = 'slow';
    const DURATION_NORMAL = 'normal';
    const DURATION_FAST = 'fast';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'datePicker';
        $this->addClasses(array(
            Css::ICONEDIT,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setIcon(Css::ICON_CALENDAR, self::POSITION_RIGHT);
        $this->jQueryParams['changeYear'] = true;
        $this->jQueryParams['changeMonth'] = true;
        $this->setDateFormat('yyyy-MM-dd');
    }

    /**
     * Ustawia język kalendarza
     * 
     * @param string $locale
     * @return \ZendY\Form\Element\DatePicker
     */
    public function setLocale($locale) {
        $this->setJQueryParam(self::PARAM_LOCALE, $locale);
        return $this;
    }

    /**
     * Zwraca kod ustawień językowych
     * 
     * @return string
     */
    public function getLocale() {
        return $this->getJQueryParam(self::PARAM_LOCALE);
    }

    /**
     * Ustawia format daty wyświetlanej w kontrolce
     * 
     * @param string $dateFormat
     * @return \ZendY\Form\Element\DatePicker
     */
    public function setDateFormat($dateFormat) {
        $this->setJQueryParam(self::PARAM_DATEFORMAT, $dateFormat);
        $this->removeValidator('date');
        $this->addValidator(new \Zend_Validate_Date(array('format' => $dateFormat)), true);
        return $this;
    }

    /**
     * Zwraca format daty w kontrolce
     * 
     * @return string
     */
    public function getDateFormat() {
        return $this->getJQueryParam(self::PARAM_DATEFORMAT);
    }

    /**
     * Ustawia minimalną datę
     * 
     * @param string $minDate
     * @return \ZendY\Form\Element\DatePicker
     */
    public function setMinDate($minDate) {
        $this->setJQueryParam(self::PARAM_MINDATE, $minDate);
        return $this;
    }

    /**
     * Zwraca minimalną datę kontrolki
     * 
     * @return string
     */
    public function getMinDate() {
        return $this->getJQueryParam(self::PARAM_MINDATE);
    }

    /**
     * Ustawia maksymalną datę
     * 
     * @param string $maxDate
     * @return \ZendY\Form\Element\DatePicker
     */
    public function setMaxDate($maxDate) {
        $this->setJQueryParam(self::PARAM_MAXDATE, $maxDate);
        return $this;
    }

    /**
     * Zwraca maksymalną datę kontrolki
     * 
     * @return string
     */
    public function getMaxDate() {
        return $this->getJQueryParam(self::PARAM_MAXDATE);
    }

    /**
     * Ustawia czas włączania/wyłączania kalendarza
     * 
     * @param int|string $duration
     * @return \ZendY\Form\Element\DatePicker
     */
    public function setDuration($duration) {
        $this->setJQueryParam(self::PARAM_DURATION, $duration);
        return $this;
    }

    /**
     * Zwraca czas włączania/wyłączania kalendarza
     * 
     * @return int|string
     */
    public function getDuration() {
        return $this->getJQueryParam(self::PARAM_DURATION);
    }

    /**
     * Ustawia, czy kontrolka ma być tylko do odczytu
     * 
     * @param bool $readOnly
     * @return \ZendY\Form\Element\DatePicker
     */
    public function setReadOnly($readOnly) {
        parent::setReadOnly($readOnly);
        if ($readOnly) {
            $this->setMinDate(-1);
            $this->setMaxDate(-2);
        } else {
            $this->setMinDate(NULL);
            $this->setMaxDate(NULL);
        }
        return $this;
    }

    /**
     * Ustawia, czy kontrolka ma być wyłączona
     * 
     * @param bool $disabled
     * @return \ZendY\Form\Element\DatePicker
     */
    public function setDisabled($disabled) {
        parent::setDisabled($disabled);
        if ($disabled) {
            $this->setMinDate(-1);
            $this->setMaxDate(-2);
        } else {
            $this->setMinDate(NULL);
            $this->setMaxDate(NULL);
        }
        return $this;
    }

}
