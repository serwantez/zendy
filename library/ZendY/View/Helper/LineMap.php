<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

/**
 * Pomocnik do wygenerowania mapy wyświetlającej linię
 *
 * @author Piotr Zając
 */
class LineMap extends Map {

    /**
     * Generuje kod mapy wyświetlającej linię
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function lineMap($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $jh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $params['id'] = $attribs['id'];
        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["mp"]["%s"] = %s("#%s").lineMap(%s);'
                , $id
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $id . '-map'
                , $params);

        unset($attribs['id']);
        unset($attribs['name']);
        unset($attribs['value']);
        unset($attribs['options']);

        $attribs = $this->_extractAttributes($attribs);
        $attribs['inner']['class'] = Css::MAP_CANVAS;
        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s</span>';

        $div = $this->view->div($id . '-map', $attribs['inner']);
        $hidden = $this->view->formHidden($id, $value);

        $html = sprintf($container, $div, $hidden);
        
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>'. $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }         

        return $html;
    }

}

