<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania kontrolki Hidden
 *
 * @author Piotr Zając
 */
class Hidden extends Widget {

    public function hidden($name, $value = null, array $params = array(), array $attribs = array()) {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $params = $this->_prepareParams($name, $params);

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $html = $this->_hidden($name, $value, $attribs);

        $js = sprintf('dc["hd"]["%s"] = new hidden("%s",%s);', $name, $name, $params);

        $this->jquery->addJavascriptFile($this->view->host .
                '/library/components/hidden/hidden.js');

        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>' . $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }

        return $html;
    }

}
