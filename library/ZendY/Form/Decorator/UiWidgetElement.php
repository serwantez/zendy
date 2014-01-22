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
 * Dekorator formularza dla wszystkich kontrolek jednowartościowych
 *
 * @author Piotr Zając
 */
class UiWidgetElement extends \ZendX_JQuery_Form_Decorator_UiWidgetElement {

    /**
     * Render an jQuery UI Widget element using its associated view helper
     *
     * @param  string $content
     * @return string
     * @throws Zend_Form_Decorator_Exception if element or view are not registered
     */
    public function render($content) {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
            require_once 'Zend/Form/Decorator/Exception.php';
            throw new Zend_Form_Decorator_Exception('UiWidgetElement decorator cannot render without a registered view object');
        }

        if (method_exists($element, 'getMultiOptions')) {
            $element->getMultiOptions();
        }

        if (method_exists($element, 'getJQueryParams')) {
            $this->setJQueryParams($element->getJQueryParams());
        }
        $jQueryParams = $this->getJQueryParams();

        $helper = $this->getHelper();
        $separator = $this->getSeparator();
        $value = $this->getValue($element);
        $attribs = $this->getElementAttribs();
        $name = $element->getFullyQualifiedName();

        $helperObject = $view->getHelper($helper);
        if (method_exists($helperObject, 'setTranslator')) {
            $helperObject->setTranslator($element->getTranslator());
        }

        $id = $element->getId();
        $attribs['id'] = $id;

        $elementContent = $view->$helper($name, $value, $jQueryParams, $attribs, $element->options);
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