<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Element;

/**
 * Base class for multi-option report elements
 * 
 * @author Piotr Zając
 */
abstract class Multi extends Xhtml {

    /**
     * Array of options for multi-item
     * 
     * @var array
     */
    public $options = array();

    /**
     * Separator to use between options; defaults to '<br />'.
     * 
     * @var string
     */
    protected $_separator = '<br />';

    /**
     * Which values are translated already?
     * 
     * @var array
     */
    protected $_translated = array();

    /**
     * Retrieve separator
     *
     * @return mixed
     */
    public function getSeparator() {
        return $this->_separator;
    }

    /**
     * Set separator
     * 
     * @param mixed $separator
     * @return \ZendY\Report\Element\Multi
     */
    public function setSeparator($separator) {
        $this->_separator = $separator;
        return $this;
    }

    /**
     * Retrieve options array
     *
     * @return array
     */
    protected function _getMultiOptions() {
        if (null === $this->options || !is_array($this->options)) {
            $this->options = array();
        }

        return $this->options;
    }

    /**
     * Add an option
     *
     * @param  string $option
     * @param  string|null $value
     * @return ZendY_Report_Element_Multi
     */
    public function addMultiOption($option, $value = '') {
        $option = (string) $option;
        $this->_getMultiOptions();
        if (!$this->_translateOption($option, $value)) {
            $this->options[$option] = $value;
        }

        return $this;
    }

    /**
     * Add many options at once
     *
     * @param  array $options
     * @return \ZendY\Report\Element\Multi
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
     * Set all options at once (overwrites)
     *
     * @param  array $options
     * @return \ZendY\Report\Element\Multi
     */
    public function setMultiOptions(array $options) {
        $this->clearMultiOptions();
        return $this->addMultiOptions($options);
    }

    /**
     * Retrieve single multi option
     *
     * @param  string $option
     * @return mixed|null
     */
    public function getMultiOption($option) {
        $option = (string) $option;
        $this->_getMultiOptions();
        if (isset($this->options[$option])) {
            $this->_translateOption($option, $this->options[$option]);
            return $this->options[$option];
        }

        return null;
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getMultiOptions() {
        $this->_getMultiOptions();
        foreach ($this->options as $option => $value) {
            $this->_translateOption($option, $value);
        }
        return $this->options;
    }

    /**
     * Remove a single multi option
     *
     * @param  string $option
     * @return bool
     */
    public function removeMultiOption($option) {
        $option = (string) $option;
        $this->_getMultiOptions();
        if (isset($this->options[$option])) {
            unset($this->options[$option]);
            if (isset($this->_translated[$option])) {
                unset($this->_translated[$option]);
            }
            return true;
        }

        return false;
    }

    /**
     * Clear all options
     *
     * @return \ZendY\Report\Element\Multi
     */
    public function clearMultiOptions() {
        $this->options = array();
        $this->_translated = array();
        return $this;
    }

    /**
     * Translate an option
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
            $this->options[$option] = $this->_translateValue($value);
            if ($this->options[$option] === $value) {
                return false;
            }
            $this->_translated[$option] = true;
            return true;
        }

        return false;
    }

    /**
     * Translate a multi option value
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
                array('Multi')
            ));
        }
    }

    /**
     * Tworzy listę wartości
     */
    abstract protected function _performList();

    /**
     * Renderuje kontrolkę
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        $this->_performList();
        return parent::render($view);
    }

}
