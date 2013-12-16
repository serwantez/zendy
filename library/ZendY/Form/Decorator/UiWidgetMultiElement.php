<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Decorator;

use ZendY\Exception;

require_once "ZendX/JQuery/Form/Decorator/UiWidgetElement.php";

/**
 * Dekorator formularza dla wszystkich kontrolek wielowartościowych
 *
 * @author Piotr Zając
 */
class UiWidgetMultiElement extends \ZendX_JQuery_Form_Decorator_UiWidgetElement {

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
                if (array_key_exists('multiOptions', $this->_attribs)) {
                    $this->setMultiOptions($this->_attribs['multiOptions']);
                    unset($this->_attribs['multiOptions']);
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
     * @return \ZendY\Form\Decorator\UiWidgetMultiElement
     */
    public function setMultiOption($key, $value) {
        $this->_multiOptions[(string) $key] = $value;
        return $this;
    }

    /**
     * Add many multioptions at once
     * 
     * @param array $params
     * @return \ZendY\Form\Decorator\UiWidgetMultiElement
     */
    public function addMultiOptions(array $params) {
        $this->_multiOptions = array_merge($this->_multiOptions, $params);
        return $this;
    }

    /**
     * Set many multioptions at once
     * 
     * @param array $params
     * @return \ZendY\Form\Decorator\UiWidgetMultiElement
     */
    public function setMultiOptions(array $params) {
        $this->_multiOptions = $params;
        return $this;
    }

    /**
     * Retrieve a multioption
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
     * Return all multioptions
     * 
     * @return array
     */
    public function getMultiOptions() {
        $this->getElementAttribs();
        return $this->_multiOptions;
    }

    /**
     * Render an jQuery UI Widget element using its associated view helper
     *
     * @param string $content
     * @return string
     */
    public function render($content) {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
            throw new Exception('UiWidgetMultiElement decorator cannot render without a registered view object');
        }

        if (method_exists($element, 'getJQueryParams')) {
            $this->setJQueryParams($element->getJQueryParams());
        }
        $jQueryParams = $this->getJQueryParams();
        $multiOptions = $this->getMultiOptions();

        $helper = $this->getHelper();
        $separator = $this->getSeparator();
        $value = $this->getValue($element);
        $attribs = $this->getElementAttribs();
        $name = $element->getFullyQualifiedName();
        $id = $element->getId();
        $attribs['id'] = $id;

        //print_r($attribs);
        $elementContent = $view->$helper($name, $value, $jQueryParams, $attribs, $multiOptions);
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