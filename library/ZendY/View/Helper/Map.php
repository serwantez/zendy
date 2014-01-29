<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Form\Element\CustomMap;

/**
 * Klasa bazowa dla pomocników generujących mapy
 *
 * @author Piotr Zając
 */
abstract class Map extends Widget {

    /**
     * Przygotowuje parametry map
     * 
     * @param string $id
     * @param array $params
     * @return array
     */
    protected function _prepareParams($id, array $params) {
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/map/map.css');
        $this->jquery->addJavascriptFile('http://maps.google.com/maps/api/js?sensor=true');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/map/map.js');
        $params = parent::_prepareParams($id, $params);
        if (array_key_exists('map', $params)) {
            if (array_key_exists(CustomMap::PARAM_CENTER, $params['map'])) {
                $params['map'][CustomMap::PARAM_CENTER] = new \Zend_Json_Expr(sprintf('new google.maps.LatLng(%s,%s)'
                                        , $params['map'][CustomMap::PARAM_CENTER][0]
                                        , $params['map'][CustomMap::PARAM_CENTER][1]));
            }

            if (array_key_exists(CustomMap::PARAM_MAPTYPEID, $params['map'])) {
                $params['map'][CustomMap::PARAM_MAPTYPEID] = new \Zend_Json_Expr(
                                $params['map'][CustomMap::PARAM_MAPTYPEID]
                );
            }
        }

        if (array_key_exists(CustomMap::PARAM_HIDDENCONTAINER, $params)) {
            unset($params[CustomMap::PARAM_HIDDENCONTAINER]);
        }

        return $params;
    }

}
