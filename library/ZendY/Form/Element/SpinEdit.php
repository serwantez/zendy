<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka wyboru liczby z policzalnego przedziału liczb dyskretnych
 *
 * @author Piotr Zając
 */
class SpinEdit extends CustomEdit {

    use \ZendY\ControlTrait;

    /**
     * Parametry
     */

    const PARAM_MIN = 'min';
    const PARAM_MAX = 'max';
    const PARAM_ICONS = 'icons';
    const PARAM_STEP = 'step';
    const PARAM_PAGE = 'page';
    const PARAM_LOCALE = 'culture';
    const PARAM_NUMBERFORMAT = 'numberFormat';

    /**
     * Formaty liczb
     */
    const FORMAT_NUMBER = 'n';
    const FORMAT_DECIMALDIGITS = 'd';
    const FORMAT_PERCENTAGE = 'p';
    const FORMAT_CURRENCY = 'c';

    /**
     * Szerokość przycisków
     * 
     * @var int
     */
    protected $_buttonWidth = 21;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'spinEdit';
        $this->setMin(0);
        $this->setMax(100);
        $this->jQueryParams[self::PARAM_ICONS] = array(
            'down' => Css::ICON_TRIANGLE1S,
            'up' => Css::ICON_TRIANGLE1N
        );
        if (\Zend_Registry::isRegistered('Zend_Locale')) {
            $this->setLocale(\Zend_Registry::get('Zend_Locale')->getLanguage());
        }
    }

    /**
     * Ustawia minimalną wartość
     * 
     * @param int|float $value
     * @return \ZendY\Form\Element\SpinEdit
     */
    public function setMin($value) {
        $this->setJQueryParam(self::PARAM_MIN, $value);
        return $this;
    }

    /**
     * Zwraca minimalną wartość
     * 
     * @return int|float
     */
    public function getMin() {
        return $this->getJQueryParam(self::PARAM_MIN);
    }

    /**
     * Ustawia maksymalną wartość
     * 
     * @param int|float $value
     * @return \ZendY\Form\Element\SpinEdit
     */
    public function setMax($value) {
        $this->setJQueryParam(self::PARAM_MAX, $value);
        return $this;
    }

    /**
     * Zwraca maksymalną wartość
     * 
     * @return int|float
     */
    public function getMax() {
        return $this->getJQueryParam(self::PARAM_MAX);
    }

    /**
     * Ustawia najmniejszy krok przejścia do następnej wartości (strzałki kursora)
     * 
     * @param int|float $step
     * @return \ZendY\Form\Element\SpinEdit
     */
    public function setStep($step) {
        $this->setJQueryParam(self::PARAM_STEP, $step);
        return $this;
    }

    /**
     * Zwraca najmniejszy krok przejścia do następnej wartości
     * 
     * @return int|float
     */
    public function getStep() {
        return $this->getJQueryParam(self::PARAM_STEP);
    }

    /**
     * Ustawia duży krok przejścia do następnej wartości (PageUp, PageDown)
     * 
     * @param int|float $step
     * @return \ZendY\Form\Element\SpinEdit
     */
    public function setPage($step) {
        $this->setJQueryParam(self::PARAM_PAGE, $step);
        return $this;
    }

    /**
     * Zwraca duży krok przejścia do następnej wartości
     * 
     * @return int|float
     */
    public function getPage() {
        return $this->getJQueryParam(self::PARAM_PAGE);
    }

    /**
     * Ustawia kod języka
     * 
     * @link https://github.com/jquery/globalize#globalizecultures
     * @param string $locale
     * @return \ZendY\Form\Element\SpinEdit
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
     * Ustawia format liczb
     * przykładowe kody: 
     * n - liczba, 
     * n2 - liczba z dwoma miejscami po przecinku, 
     * c - waluta, 
     * d - cyfry, 
     * p - procent
     * 
     * @link https://github.com/jquery/globalize#numbers
     * @param string $format
     * @return \ZendY\Form\Element\SpinEdit
     */
    public function setFormat($format) {
        $this->setJQueryParam(self::PARAM_NUMBERFORMAT, $format);
        return $this;
    }

    /**
     * Zwraca format liczb
     * 
     * @return string
     */
    public function getFormat() {
        return $this->getJQueryParam(self::PARAM_NUMBERFORMAT);
    }

    /**
     * Ustawia szerokość kontrolki
     * 
     * @param int $width
     * @param string|null $unit
     * @return \ZendY\Form\Element\SpinEdit
     */
    public function setWidth($value, $unit = 'px') {
        $value -= $this->_buttonWidth;
        parent::setWidth($value, $unit);
        return $this;
    }

    /**
     * Zwraca szerokość kontrolki
     * 
     * @return array
     */
    public function getWidth() {
        $width = parent::getWidth();
        $width['value'] += $this->_buttonWidth;
        return $width;
    }

}