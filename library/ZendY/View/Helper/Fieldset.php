<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

require_once 'Zend/View/Helper/Fieldset.php';

/**
 * Pomocnik do wygenerowania grupy pól formularza (Fieldset)
 *
 * @author Piotr Zając
 */
class Fieldset extends \Zend_View_Helper_Fieldset {

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

