<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Decorator;

use ZendY\Exception;

/**
 * Dekorator wstęgi raportu
 *
 * @author Piotr Zając
 */
class Multi extends ViewHelper {

    /**
     * Element attributes
     *
     * @var array
     */
    protected $_attribs;

    /**
     * Multioptions
     *
     * @var array
     */
    protected $_multiOptions = array();

    /**
     * Get element attributes
     *
     * @return array
     */
    public function getElementAttribs() {
        if (null === $this->_attribs) {
            if ($this->_attribs = parent::getElementAttribs()) {
                if (array_key_exists('options', $this->_attribs)) {
                    $this->setMultiOptions($this->_attribs['options']);
                    unset($this->_attribs['options']);
                }
            }
        }

        return $this->_attribs;
    }

    /**
     * Set a multioption
     * 
     * @param string $key
     * @param mixed $value
     * @return \ZendY\Report\Decorator\Multi
     */
    public function setMultiOption($key, $value) {
        $this->_multiOptions[(string) $key] = $value;
        return $this;
    }

    /**
     * Add many multioptions at once
     * 
     * @param array $options
     * @return \ZendY\Report\Decorator\Multi
     */
    public function addMultiOptions(array $options) {
        $this->_multiOptions = array_merge($this->_multiOptions, $options);
        return $this;
    }

    /**
     * Set multioptions
     * @param array $options
     * @return \ZendY\Report\Decorator\Multi
     */
    public function setMultiOptions(array $options) {
        $this->_multiOptions = $options;
        return $this;
    }

    /**
     * Get a multioption
     * 
     * @param string $key
     * @return mixed|null
     */
    public function getMultiOption($key) {
        $this->getElementAttribs();
        $key = (string) $key;
        if (array_key_exists($key, $this->_multiOptions)) {
            return $this->_multiOptions[$key];
        }

        return null;
    }

    /**
     * Get all multioptions
     * 
     * @return array
     */
    public function getMultiOptions() {
        $this->getElementAttribs();
        return $this->_multiOptions;
    }

    /**
     * Renderuje dekorator
     * 
     * @param string $content
     * @return string
     * @throws Exception
     */
    public function render($content) {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
            throw new Exception('Multi decorator cannot render without a registered view object');
        }

        $multiOptions = $this->getMultiOptions();

        $helper = $this->getHelper();
        $separator = $this->getSeparator();
        $value = $this->getValue($element);
        $attribs = $this->getElementAttribs();
        $name = $element->getFullyQualifiedName();
        $id = $element->getId();
        $attribs['id'] = $id;

        $elementContent = $view->$helper($name, $value, $attribs, $multiOptions);
        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $separator . $elementContent;
            case self::PREPEND:
                return $elementContent . $separator . $content;
            default:
                return $elementContent;
        }
    }

}