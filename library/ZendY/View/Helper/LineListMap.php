<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

/**
 * Pomocnik do wygenerowania mapy wyświetlającej listę obiektów
 *
 * @author Piotr Zając
 */
class LineListMap extends Map {

    /**
     * Generuje kod mapy wyświetlającej listę obiektów
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @param array|null $options
     * @param string|null $listsep
     * @return string
     */
    public function lineListMap($id, $value = null, $params = null, $attribs = null, $options = null, $listsep = "") {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $jh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $params['id'] = $attribs['id'];
        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["mp"]["%s"] = %s("#%s").lineListMap(%s);'
                , $id
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $id . '_map'
                , $params);

        unset($attribs['id']);
        unset($attribs['name']);
        unset($attribs['value']);
        unset($attribs['options']);

        $attribs = $this->_extractAttributes($id, $attribs);
        $attribs['inner']['class'] = Css::MAP_CANVAS;
        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s</span>';

        $div = $this->view->div($id . '_map', $attribs['inner']);
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

