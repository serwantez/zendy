<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Decorator;

use ZendY\Exception;
use ZendY\Report;
use ZendY\Report\Decorator\BaseInterface;
use ZendY\Report\Element;

/**
 * ZendY_Report_Decorator_Base
 */
abstract class Base implements BaseInterface {
    /**
     * Placement constants
     */

    const APPEND = 'APPEND';
    const PREPEND = 'PREPEND';

    /**
     * Default placement: append
     * 
     * @var string
     */
    protected $_placement = 'APPEND';

    /**
     * Kontrolka raportu
     * 
     * @var ZendY_Report_Element|Report
     */
    protected $_element;

    /**
     * Opcje dekoratora
     * 
     * @var array
     */
    protected $_options = array();

    /**
     * Separator between new content and old
     * 
     * @var string
     */
    protected $_separator = PHP_EOL;

    /**
     * Constructor
     *
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof \Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Set options
     *
     * @param  array $options
     * @return \ZendY\Report\Decorator\Base
     */
    public function setOptions(array $options) {
        $this->_options = $options;
        return $this;
    }

    /**
     * Set options from config object
     *
     * @param  \Zend_Config $config
     * @return \ZendY\Report\Decorator\Base
     */
    public function setConfig(\Zend_Config $config) {
        return $this->setOptions($config->toArray());
    }

    /**
     * Set option
     *
     * @param  string $key
     * @param  mixed $value
     * @return \ZendY\Report\Decorator\Base
     */
    public function setOption($key, $value) {
        $this->_options[(string) $key] = $value;
        return $this;
    }

    /**
     * Get option
     *
     * @param  string $key
     * @return mixed
     */
    public function getOption($key) {
        $key = (string) $key;
        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }

        return null;
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * Remove single option
     *
     * @param mixed $key
     * @return bool
     */
    public function removeOption($key) {
        if (null !== $this->getOption($key)) {
            unset($this->_options[$key]);
            return true;
        }

        return false;
    }

    /**
     * Clear all options
     *
     * @return \ZendY\Report\Decorator\Base
     */
    public function clearOptions() {
        $this->_options = array();
        return $this;
    }

    /**
     * Set current form element
     *
     * @param  \ZendY\Report\Element|\ZendY\Report $element
     * @return \ZendY\Report\Decorator\Base
     * @throws Exception on invalid element type
     */
    public function setElement($element) {
        if ((!$element instanceof Element)
                && (!$element instanceof Report)) {
            throw new Exception('Invalid element type passed to decorator');
        }

        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve current element
     *
     * @return \ZendY\Report\Element|\ZendY\Report
     */
    public function getElement() {
        return $this->_element;
    }

    /**
     * Determine if decorator should append or prepend content
     *
     * @return string
     */
    public function getPlacement() {
        $placement = $this->_placement;
        if (null !== ($placementOpt = $this->getOption('placement'))) {
            $placementOpt = strtoupper($placementOpt);
            switch ($placementOpt) {
                case self::APPEND:
                case self::PREPEND:
                    $placement = $this->_placement = $placementOpt;
                    break;
                case false:
                    $placement = $this->_placement = null;
                    break;
                default:
                    break;
            }
            $this->removeOption('placement');
        }

        return $placement;
    }

    /**
     * Retrieve separator to use between old and new content
     *
     * @return string
     */
    public function getSeparator() {
        $separator = $this->_separator;
        if (null !== ($separatorOpt = $this->getOption('separator'))) {
            $separator = $this->_separator = (string) $separatorOpt;
            $this->removeOption('separator');
        }
        return $separator;
    }

    /**
     * Decorate content and/or element
     *
     * @param  string $content
     * @return void
     * @throws ZendY_Exception when unimplemented
     */
    public function render($content) {
        throw new Exception('render() not implemented');
    }

}
