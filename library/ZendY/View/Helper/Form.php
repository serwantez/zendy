<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

require_once 'Zend/View/Helper/Form.php';

/**
 * Pomocnik do wygenerowania formularza
 *
 * @author Piotr Zając
 */
class Form extends \Zend_View_Helper_Form {

    /**
     * Render HTML form
     *
     * @param  string $name Form name
     * @param  null|array $attribs HTML form attributes
     * @param  false|string $content Form content
     * @return string
     */
    public function form($name, $attribs = null, $content = false) {
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/form/form.css');
        return parent::form($name, $attribs, $content);
    }

    /**
     * Converts an associative array to a string of tag attributes.
     * 
     * @param array $attribs
     * @return string
     */
    protected function _htmlAttribs($attribs) {
        $attribs = Widget::prepareCSS($attribs);
        return parent::_htmlAttribs($attribs);
    }

}

