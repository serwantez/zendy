<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;
use ZendY\Form\Element;
use ZendY\Form\Container;

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
        $label = $this->view->translate($attribs['label']);

        unset($attribs['id']);
        unset($attribs['name']);
        unset($attribs['value']);
        unset($attribs['label']);
        unset($attribs['options']);

        $attribs = $this->_extractAttributes($id, $attribs);
        $attribs['inner']['class'] = Css::MAP_CANVAS;
        $attribs['outer']['id'] = $attribs['outer']['id'] . '-outer';
        $container = '<div %s'
                . '><div class="%s">%s%s</div>
                    %s</div>%s';

        $div = $this->view->div($id . '-map', $attribs['inner']);
        $valuer = new Element\Edit($id, array(
                    Element\Edit::PROPERTY_VALUE => $value,
                    Element\Edit::PROPERTY_LABEL => array(
                        'text' => $label,
                        'width' => 110
                    ),
                    Element\Edit::PROPERTY_WIDTH => 250
                ));
        $button = new Element\IconButton($id . '-button', array(
                    Element\IconButton::PROPERTY_LABEL => 'Details',
                    Element\IconButton::PROPERTY_ICONS => Css::ICON_PENCIL,
                    Element\IconButton::PROPERTY_CLASSES => array(
                        Css::MAP_BUTTON,
                    ),
                ));
        $dialog = new Container\Dialog(array(
                    Container\Dialog::PROPERTY_NAME => $id . '-details',
                    Container\Dialog::PROPERTY_TITLE => 'Details',
                    Container\Dialog::PROPERTY_WIDTH => 400,
                    Container\Dialog::PROPERTY_HEIGHT => 150,
                    Container\Dialog::PROPERTY_OPENERS => array($button),
                ));
        $dialog->addElement($valuer);
        $dialog->onContain();

        $html = sprintf($container
                , $this->_htmlAttribs($attribs['outer'])
                , Css::MAP_HEADER . ' ' . Css::WIDGET_HEADER . ' ' . Css::CORNER_ALL . ' ' . Css::HELPER_CLEARFIX
                , '<span class="' . Css::MAP_TITLE . '">' . $label . '</span>'
                , $button->render($this->view)
                , $div
                , $dialog->render($this->view)
        );

        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>' . $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }

        return $html;
    }

}

