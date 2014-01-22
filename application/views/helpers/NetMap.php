<?php

require_once 'ZendY/View/Helper/Map.php';

/**
 * Pomocnik mapy wyświetlającej sieć kolejową
 *
 * @author Piotr Zając
 */
class Application_View_Helper_NetMap extends ZendY_View_Helper_Map {

    public function netMap($id, $value = null, $params = null, $attribs = null, $options = null, $listsep = "") {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $jh = ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $params['id'] = $attribs['id'];
        if (count($params) > 0) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js[] = sprintf('dn["mp"]["%s"] = %s("#%s").netMap(%s);'
                , $id
                , ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $id . '_map'
                , $params);

        foreach ((array) $options as $key => $fields) {
            $js[] = sprintf('dn["mp"]["%s"].addPoint("%s",%s);'
                    , $id
                    , $key
                    , ZendX_JQuery::encodeJson($fields));
        }

        $this->jquery->addJavascriptFile('/library/components/map/netMap.js');
        $this->jquery->addOnLoad(implode("\n", $js));

        unset($attribs['id']);
        unset($attribs['name']);
        unset($attribs['value']);
        unset($attribs['options']);

        $attribs = $this->_extractAttributes($attribs);
        $attribs['inner']['class'] = 'ui-map-canvas';
        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s</span>';

        $div = $this->view->div($id . '_map', $attribs['inner']);
        $hidden = $this->view->formHidden($id, $value);

        $html = sprintf($container, $div, $hidden);

        return $html;
    }

}

