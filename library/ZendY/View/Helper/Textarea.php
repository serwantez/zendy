<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania wielolinijkowego pola tekstowego
 * 
 * @author Piotr Zając
 */
class Textarea extends Widget {

    /**
     * Generuje kod kontrolki Textarea
     * 
     * @param string $name
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function textarea($name, $value = null, array $params = array(), array $attribs = array()) {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable        
        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $params = $this->_prepareParams($name, $params);
        $attribs = $this->_extractAttributes($id, $attribs);

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["ta"]["%s"] = new edit("%s",%s);', $name, $name, $params);

        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</span>';
        $ta = '<textarea name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs['inner']) . '>'
                . $this->view->escape($value) . '</textarea>';
        $html = sprintf($container, $ta);
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/textarea/textarea.css');

        $this->jquery->addJavascriptFile($this->view->host .
                '/library/components/textarea/textarea.js');
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>' . $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }

        return $html;
    }

}
