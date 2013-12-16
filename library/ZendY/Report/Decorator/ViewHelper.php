<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Decorator;

use ZendY\Exception;
use ZendY\Report\Element;

/**
 * View Helper Decorator
 *
 * Decorate an element by using a view helper to render it.
 *
 */
class ViewHelper extends Base {

    /**
     * View helper to use when rendering
     * 
     * @var string
     */
    protected $_helper;

    /**
     * Set view helper to use when rendering
     * 
     * @param string $helper
     * @return \ZendY\Report\Decorator\ViewHelper
     */
    public function setHelper($helper) {
        $this->_helper = (string) $helper;
        return $this;
    }

    /**
     * Retrieve view helper for rendering element
     *
     * @return string
     */
    public function getHelper() {
        if (null === $this->_helper) {
            $options = $this->getOptions();
            if (isset($options['helper'])) {
                $this->setHelper($options['helper']);
                $this->removeOption('helper');
            } else {
                $element = $this->getElement();
                if (null !== $element) {
                    if (null !== ($helper = $element->getAttrib('helper'))) {
                        $this->setHelper($helper);
                    } else {
                        $type = $element->getType();
                        if ($pos = strrpos($type, '_')) {
                            $type = substr($type, $pos + 1);
                        }
                        $this->setHelper('report' . ucfirst($type));
                    }
                }
            }
        }

        return $this->_helper;
    }

    /**
     * Get name
     *
     * If element is a ZendY\Report\Element, will attempt to namespace it if the
     * element belongs to an array.
     *
     * @return string
     */
    public function getName() {
        if (null === ($element = $this->getElement())) {
            return '';
        }

        $name = $element->getName();

        if (!$element instanceof Element) {
            return $name;
        }

        if (null !== ($belongsTo = $element->getBelongsTo())) {
            $name = $belongsTo . '['
                    . $name
                    . ']';
        }

        if ($element->isArray()) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Retrieve element attributes
     *
     * Set id to element name and/or array item.
     *
     * @return array
     */
    public function getElementAttribs() {
        if (null === ($element = $this->getElement())) {
            return null;
        }

        $attribs = $element->getAttribs();
        if (isset($attribs['helper'])) {
            unset($attribs['helper']);
        }

        if (method_exists($element, 'getSeparator')) {
            if (null !== ($listsep = $element->getSeparator())) {
                $attribs['listsep'] = $listsep;
            }
        }

        if (isset($attribs['id'])) {
            return $attribs;
        }

        $id = $element->getName();

        if ($element instanceof Element) {
            if (null !== ($belongsTo = $element->getBelongsTo())) {
                $belongsTo = preg_replace('/\[([^\]]+)\]/', '-$1', $belongsTo);
                $id = $belongsTo . '-' . $id;
            }
        }

        $element->setAttrib('id', $id);
        $attribs['id'] = $id;

        return $attribs;
    }

    /**
     * Get value
     *
     * @param  \ZendY\Report\Element $element
     * @return string|null
     */
    public function getValue($element) {
        if (!$element instanceof Element) {
            return null;
        }

        return $element->getValue();
    }

    /**
     * Render an element using a view helper
     *
     * Determine view helper from 'viewHelper' option, or, if none set, from
     * the element type. Then call as
     * helper($element->getName(), $element->getValue(), $element->getAttribs())
     *
     * @param  string $content
     * @return string
     * @throws Exception if element or view are not registered
     */
    public function render($content) {
        $element = $this->getElement();

        $view = $element->getView();
        if (null === $view) {
            throw new Exception('ViewHelper decorator cannot render without a registered view object');
        }

        if (method_exists($element, 'getMultiOptions')) {
            $element->getMultiOptions();
        }

        $helper = $this->getHelper();
        $separator = $this->getSeparator();
        $value = $this->getValue($element);
        $attribs = $this->getElementAttribs();
        $name = $element->getFullyQualifiedName();
        $id = $element->getId();
        $attribs['id'] = $id;

        $helperObject = $view->getHelper($helper);
        if (method_exists($helperObject, 'setTranslator')) {
            $helperObject->setTranslator($element->getTranslator());
        }

        $elementContent = $view->$helper($name, $value, $attribs, $element->options);

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
