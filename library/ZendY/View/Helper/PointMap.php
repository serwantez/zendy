<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

/**
 * Pomocnik do wygenerowania mapy wyświetlającej punkt
 *
 * @author Piotr Zając
 */
class PointMap extends Map {

    /**
     * Generuje kod mapy wyświetlającej punkt
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function pointMap($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);
        $jh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $params['id'] = $attribs['id'];
        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["mp"]["%s"] = %s("#%s").pointMap(%s);'
                , $id
                , $jh
                , $id . '-map'
                , $params);

        unset($attribs['id']);
        unset($attribs['name']);
        unset($attribs['value']);
        unset($attribs['options']);

        $attribs = $this->_extractAttributes($id, $attribs);
        $attribs['inner']['class'] = Css::MAP_CANVAS;
        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '><div class="%s %s">%s</div>
                    %s</div>';

        $div = $this->view->div($id . '-map', $attribs['inner']);
        $inputAttribs = array();
        $hidden = $this->view->formText($id, $value, $inputAttribs);

        $html = sprintf($container, Css::WIDGET_HEADER, Css::MAP_HEADER, $hidden, $div);

        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>' . $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }

        return $html;
    }

}

