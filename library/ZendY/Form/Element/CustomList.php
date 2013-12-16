<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Db\Filter;
use ZendY\Css;

/**
 * Bazowa klasa dla kontrolek wielowartościowych - list
 * 
 * @author Piotr Zając
 */
abstract class CustomList extends Widget {

    /**
     * Tablica opcji (pozycji listy)
     * 
     * @var array
     */
    public $multiOptions = array();

    /**
     * Flag: autoregister inArray validator?
     * 
     * @var bool
     */
    protected $_registerInArrayValidator = true;

    /**
     * Separator używany pomiędzy opcjami; domyślnie '<br />'.
     * 
     * @var string
     */
    protected $_separator = '<br />';

    /**
     * Znak podziału kolumn na liście
     * 
     * @var string
     */
    protected $_columnSpace = ' ';

    /**
     * Które wartości są już przetłumaczone?
     * 
     * @var array
     */
    protected $_translated = array();

    /**
     * Czy ma być wyświetlane puste pole
     * 
     * @var bool 
     */
    protected $_emptyValue = false;

    /**
     * Parametry przekazywane do przeglądarki
     * 
     * @var array
     */
    protected $_frontNaviParams = array();

    /**
     * Tablica warunkowego formatowania wierszy
     * 
     * @var array
     */
    protected $_conditionalRowFormat = array();

    /**
     * Zwraca separator
     *
     * @return string
     */
    public function getSeparator() {
        return $this->_separator;
    }

    /**
     * Ustawia separator
     * 
     * @param string $separator
     * @return \ZendY\Form\Element\CustomList
     */
    public function setSeparator($separator) {
        $this->_separator = $separator;
        return $this;
    }

    /**
     * Ustawia znak podziału kolumn na liście
     * 
     * @param string $space
     * @return \ZendY\Form\Element\CustomList
     */
    public function setColumnSpace($space) {
        $this->_columnSpace = $space;
        return $this;
    }

    /**
     * Zwraca znak podziału kolumn na liście
     * 
     * @return string
     */
    public function getColumnSpace() {
        return $this->_columnSpace;
    }

    /**
     * Ustawia możliwość wyświetlania pustego pola option
     * 
     * @param bool|null $empty
     * @return \ZendY\Form\Element\CustomList
     */
    public function setEmptyValue($empty = true) {
        $this->_emptyValue = $empty;
        return $this;
    }

    /**
     * Zwraca informację o wyświetlaniu pustego pola option
     * 
     * @return bool
     */
    public function getEmptyValue() {
        return $this->_emptyValue;
    }

    /**
     * Zwraca tablicę opcji
     *
     * @return array
     */
    protected function _getMultiOptions() {
        if (null === $this->multiOptions || !is_array($this->multiOptions)) {
            $this->multiOptions = array();
        }

        return $this->multiOptions;
    }

    /**
     * Dodaje opcję
     *
     * @param  string $option
     * @param  string|null $value
     * @return \ZendY\Form\Element\CustomList
     */
    public function addMultiOption($option, $value = '') {
        $option = (string) $option;
        $this->_getMultiOptions();
        if (!$this->_translateOption($option, $value)) {
            $this->multiOptions[$option] = $value;
        }

        return $this;
    }

    /**
     * Dodaje wiele opcji na raz
     *
     * @param  array $options
     * @return \ZendY\Form\Element\CustomList
     */
    public function addMultiOptions(array $options) {
        foreach ($options as $option => $value) {
            if (is_array($value)
                    && array_key_exists('key', $value)
                    && array_key_exists('value', $value)
            ) {
                $this->addMultiOption($value['key'], $value['value']);
            } else {
                $this->addMultiOption($option, $value);
            }
        }
        return $this;
    }

    /**
     * Ustawia opcje
     *
     * @param  array $options
     * @return \ZendY\Form\Element\CustomList
     */
    public function setMultiOptions(array $options) {
        $this->clearMultiOptions();
        return $this->addMultiOptions($options);
    }

    /**
     * Zwraca pojedynczą opcję
     *
     * @param  string $option
     * @return mixed
     */
    public function getMultiOption($option) {
        $option = (string) $option;
        $this->_getMultiOptions();
        if (isset($this->multiOptions[$option])) {
            $this->_translateOption($option, $this->multiOptions[$option]);
            return $this->multiOptions[$option];
        }

        return null;
    }

    /**
     * Zwraca wszystkie opcje
     *
     * @return array
     */
    public function getMultiOptions() {
        $this->_getMultiOptions();
        foreach ($this->multiOptions as $option => $value) {
            $this->_translateOption($option, $value);
        }
        return $this->multiOptions;
    }

    /**
     * Usuwa pojedynczą opcję
     *
     * @param  string $option
     * @return bool
     */
    public function removeMultiOption($option) {
        $option = (string) $option;
        $this->_getMultiOptions();
        if (isset($this->multiOptions[$option])) {
            unset($this->multiOptions[$option]);
            if (isset($this->_translated[$option])) {
                unset($this->_translated[$option]);
            }
            return true;
        }

        return false;
    }

    /**
     * Usuwa wszystkie opcje
     *
     * @return \ZendY\Form\Element\CustomList
     */
    public function clearMultiOptions() {
        $this->multiOptions = array();
        $this->_translated = array();
        return $this;
    }

    /**
     * Set flag indicating whether or not to auto-register inArray validator
     *
     * @param  bool $flag
     * @return \ZendY\Form\Element\CustomList
     */
    public function setRegisterInArrayValidator($flag) {
        $this->_registerInArrayValidator = (bool) $flag;
        return $this;
    }

    /**
     * Get status of auto-register inArray validator flag
     *
     * @return bool
     */
    public function registerInArrayValidator() {
        return $this->_registerInArrayValidator;
    }

    /**
     * Is the value provided valid?
     *
     * Autoregisters InArray validator if necessary.
     *
     * @param  string $value
     * @param  mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        if ($this->registerInArrayValidator()) {
            if (!$this->getValidator('InArray')) {
                $multiOptions = $this->getMultiOptions();
                $options = array();

                foreach ($multiOptions as $opt_value => $opt_label) {
                    // optgroup instead of option label
                    if (is_array($opt_label)) {
                        $options = array_merge($options, array_keys($opt_label));
                    } else {
                        $options[] = $opt_value;
                    }
                }

                $this->addValidator(
                        'InArray', true, array($options)
                );
            }
        }
        return parent::isValid($value, $context);
    }

    /**
     * Tłumaczy opcję
     *
     * @param  string $option
     * @param  string $value
     * @return bool
     */
    protected function _translateOption($option, $value) {
        if ($this->translatorIsDisabled()) {
            return false;
        }

        if (!isset($this->_translated[$option]) && !empty($value)) {
            $this->multiOptions[$option] = $this->_translateValue($value);
            if ($this->multiOptions[$option] === $value) {
                return false;
            }
            $this->_translated[$option] = true;
            return true;
        }

        return false;
    }

    /**
     * Tłumaczy wartość opcji
     *
     * @param  string $value
     * @return string
     */
    protected function _translateValue($value) {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->_translateValue($val);
            }
            return $value;
        } else {
            if (null !== ($translator = $this->getTranslator())) {
                return $translator->translate($value);
            }

            return $value;
        }
    }

    /**
     * Ładuje domyślne dekoratory
     *
     * @return void
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->setDecorators(array(
                array('UiWidgetMultiElement'),
                array('Errors', array('tag' => 'ul', 'class' => Css::WIDGET . ' ' . Css::STATE_ERROR . ' ' . Css::CORNER_ALL)),
                array('Description', array('tag' => 'span', 'class' => 'field-description')),
                array(array('Section' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field-container'))
            ))
            ;
        }
    }

    /**
     * Ładuje dekoratory
     * 
     * @return void
     */
    public function loadDecorators() {
        $this->_labelOptions['id'] = $this->getName();
        $this->setDecorators(array(
            array('UiWidgetMultiElement'),
            array('Errors', array('tag' => 'ul', 'class' => Css::WIDGET . ' ' . Css::STATE_ERROR . ' ' . Css::CORNER_ALL)),
            array('Description', array('tag' => 'span', 'class' => 'field-description')),
            array('Label', $this->_labelOptions),
            array(array('Section' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field-container'))
        ));
    }

    /**
     * Dodaje parametr przekazywany do przeglądarki
     * 
     * @param string $paramName
     * @param string $paramValue
     * @return \ZendY_Form_Element_CustomList
     */
    public function setFrontNaviParam($paramName, $paramValue) {
        $this->_frontNaviParams[$paramName] = $paramValue;
        return $this;
    }

    /**
     * Zwraca tablicę parametrów przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontNaviParams() {
        return $this->_frontNaviParams;
    }

    /**
     * Dodaje warunek formatujący wiersze
     * 
     * @param Filter|array $condition
     * @param string $rowFormat
     * @return \ZendY\Form\Element\CustomList
     */
    public function addConditionalRowFormat($condition, $rowFormat) {
        if (is_array($condition))
            $condition = new Filter($condition);
        $this->_conditionalRowFormat[] = array($condition, $rowFormat);
        return $this;
    }

    /**
     * Zwraca warunki formatujące wiersze
     * 
     * @return array
     */
    public function getConditionalRowFormat() {
        return $this->_conditionalRowFormat;
    }

    /**
     * Formatuje kolumny danych według zdefiniowanych dekoratorów
     * 
     * @param array $data
     * @return array
     */
    public function formatData(array $data = array()) {
        return $data;
    }

}
