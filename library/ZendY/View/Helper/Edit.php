<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania kontrolki Edit
 *
 * @author Piotr Zając
 */
class Edit extends Widget {

    /**
     * Generuje kod kontrolki Edit
     * 
     * @param string $id
     * @param mixed|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function edit($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["ed"]["%s"] = new edit("%s",%s);', $id, $id, $params);

        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</span>';

        $html = sprintf($container, $this->view->formText($id, $value, $attribs['inner']));
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/edit/edit.css');
        
        $this->jquery->addJavascriptFile($this->view->host .
                '/library/components/edit/edit.js');
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>'. $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }        
        return $html;
    }

}

