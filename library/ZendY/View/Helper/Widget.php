<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

require_once 'ZendX/JQuery/View/Helper/UiWidget.php';

use ZendY\Form\Element;
use ZendY\Css;

/**
 * Bazowy pomocnik do wygenerowania pól formularza
 * 
 * @author Piotr Zając
 */
abstract class Widget extends \ZendX_JQuery_View_Helper_UiWidget {

    /**
     * Tworzy kod atrybutów css: class i style na podstawie tablicy atrybutów
     * 
     * @param array $attribs
     * @return array $attribs
     */
    public static function prepareCSS(array $attribs) {
        //zamienia atrybut align na klasę css
        if (isset($attribs['align'])) {
            //remove all align classes
            if (array_key_exists('class', $attribs) && is_array($attribs['class'])) {
                foreach ($attribs['class'] as $key => $class) {
                    if (in_array($class, Css::$aligns)) {
                        unset($attribs['class'][$key]);
                    }
                }
            } else {
                $attribs['class'] = array();
            }
            $attribs['class'][] = $attribs['align'];
            //usunięcie stałej szerokości
            if ($attribs['align'] == Css::ALIGN_BOTTOM
                    || $attribs['align'] == Css::ALIGN_TOP
                    || $attribs['align'] == Css::ALIGN_CLIENT) {
                if (isset($attribs['style']) && isset($attribs['style']['width'])) {
                    unset($attribs['style']['width']);
                }
            }
            //usunięcie stałej wysokości
            if ($attribs['align'] == Css::ALIGN_LEFT
                    || $attribs['align'] == Css::ALIGN_RIGHT
                    || $attribs['align'] == Css::ALIGN_CLIENT) {
                if (isset($attribs['style']) && isset($attribs['style']['height'])) {
                    unset($attribs['style']['height']);
                }
            }
            unset($attribs['align']);
        }

        //Klasy css mogą być podane jako tablica, należy je zamienić na ciąg rozdzielony spacjami
        if (array_key_exists('class', $attribs) && is_array($attribs['class']))
            $attribs['class'] = implode(' ', $attribs['class']);

        //Style css mogą być podane jako tablica, należy je zamienić na ciąg par rozdzielony średnikami
        if (array_key_exists('style', $attribs) && is_array($attribs['style'])) {
            $style = '';
            $i = 0;
            foreach ($attribs['style'] as $key => $value) {
                if ($i) {
                    $style .= '; ';
                }
                //w przypadku oddzielenia wartości od jednostki:
                if (is_array($value)) {
                    $value = $value['value'] . $value['unit'];
                }
                $style .= $key . ': ' . $value;
                $i++;
            }
            $attribs['style'] = $style;
        }
        return $attribs;
    }

    /**
     * Łączy wartość z jednostką pojedynczej właściwości
     * 
     * @param array $property
     * @return string
     */
    public static function implodeArrayProperty(array $property) {
        if (is_array($property)) {
            $property = $property['value'] . $property['unit'];
        }
        return $property;
    }

    /**
     * Łączy wartość z jednostką w tablicy właściwości
     * 
     * @param array $properties
     * @return string
     */
    public static function implodeArrayProperties(array $properties) {
        foreach ($properties as $key => $property) {
            if (is_array($property)) {
                $properties[$key] = $property['value'] . $property['unit'];
            }
        }
        return $properties;
    }

    /**
     * Pomaga w zbudowaniu poprawnej struktury tablicy parametrów jquery
     * - usuwa parametry pomocnicze
     * 
     * @param string $id
     * @param array $params
     * @return array
     */
    protected function _prepareParams($id, array $params) {
        $jqh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        //tooltip
        if (array_key_exists(Element\Widget::PARAM_TOOLTIP, $params)) {
            if (count($params[Element\Widget::PARAM_TOOLTIP])) {
                $params[Element\Widget::PARAM_TOOLTIP] = \ZendY\JQuery::encodeJson($params[Element\Widget::PARAM_TOOLTIP]);
            } else {
                $params[Element\Widget::PARAM_TOOLTIP] = '{}';
            }
            $js = sprintf('%s("#%s").tooltip(%s);', $jqh, $id . '-container'
                    , $params[Element\Widget::PARAM_TOOLTIP]);
            //$this->view->headLink()->appendStylesheet($this->view->host . '/library/components/tooltip/jquery.ui.tooltip.css');
            $this->jquery->addOnLoad($js);
            unset($params[Element\Widget::PARAM_TOOLTIP]);
        }

        //focus
        if (array_key_exists(Element\Widget::PARAM_FOCUS, $params)) {
            $js = sprintf('%s("#%s").focus();', $jqh, $id);
            $this->jquery->addOnLoad($js);
            unset($params[Element\Widget::PARAM_FOCUS]);
        }

        //width
        if (array_key_exists('width', $params) && is_array($params['width'])) {
            $params['width'] = self::implodeArrayProperty($params['width']);
        }

        //height
        if (array_key_exists('height', $params) && is_array($params['height'])) {
            $params['height'] = self::implodeArrayProperty($params['height']);
        }

        //label
        if (array_key_exists('label', $params)) {
            $params['label'] = $this->view->translate($params['label']);
        }
        return $params;
    }

    /**
     * Pomaga w zbudowaniu poprawnej struktury tablicy atrybutów
     *
     * @param string $id
     * @param string $value
     * @param array $attribs
     * @return array
     */
    protected function _prepareAttributes($id, $value, $attribs) {
        $attribs = self::prepareCSS($attribs);
        return parent::_prepareAttributes($id, $value, $attribs);
    }

    /**
     * Oddziela atrybuty przypisywane kontenerowi kontrolki od atrybutów kontrolki wewnętrznej
     * 
     * @param array $attribs
     * @return array
     */
    protected function _extractAttributes($id, $attribs) {
        $a = array(
            'outer' => array(),
            'inner' => array()
        );
        if (array_key_exists('class', $attribs)) {
            $a['outer']['class'] = $attribs['class'];
            unset($attribs['class']);
        }
        if (array_key_exists('style', $attribs)) {
            $a['outer']['style'] = $attribs['style'];
            unset($attribs['style']);
        }
        $a['outer']['id'] = $this->view->escape($id) . '-container';
        $a['inner'] = $attribs;
        return $a;
    }

}
