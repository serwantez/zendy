<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;
use ZendY\Form\Element;

/**
 * Pomocnik do wygenerowania kontrolki IconEdit
 * (pole tekstowe z ikoną)
 *
 * @author Piotr Zając
 */
class IconEdit extends Widget {

    /**
     * Generuje kod kontrolki IconEdit
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function iconEdit($id, $value = null, array $params = array(), array $attribs = array()) {
        //obliczenie szerokości pola tekstowego
        if (isset($attribs['style']['width'])) {
            $inputWidth = array(
                'value' => $attribs['style']['width']['value'] - Css::$iconSize - 1,
                'unit' => $attribs['style']['width']['unit']
            );
        } else {
            $inputWidth = array(
                'value' => 100,
                'unit' => '%'
            );
        }
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        $attribs = $this->_extractAttributes($attribs);
        $attribs['inner']['style'] = 'width: ' . $inputWidth['value'] . $inputWidth['unit'] . ';';

        $params = $this->_prepareParams($id, $params);

        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s</div>';

        $input = $this->view->formText($id, $value, $attribs['inner']);

        if (array_key_exists(Element\IconEdit::PARAM_ICON, $params)) {
            if ($params[Element\IconEdit::PARAM_POSITION] == Element\IconEdit::POSITION_RIGHT) {
                $icon = sprintf('<a id="%s-button" class="%s %s-%s-button %s %s %s %s %s %s" tabindex="-1" role="button">
                    <span class="%s">
                    <span class="%s %s">&#9650;</span>
                    </span>
                    </a>'
                        , $id
                        , Css::ICONEDIT_BUTTON
                        , Css::ICONEDIT
                        , $params[Element\IconEdit::PARAM_POSITION]
                        , Css::CORNER_TR
                        , Css::CORNER_BR
                        , Css::BUTTON
                        , Css::WIDGET
                        , Css::STATE_DEFAULT
                        , Css::BUTTON_TEXT_ONLY
                        , Css::BUTTON_TEXT
                        , Css::ICON
                        , $params[Element\IconEdit::PARAM_ICON]);
                $html = sprintf($container, $input, $icon);
            } else {
                $icon = sprintf('<a id="%s-button" class="%s %s-%s-button %s %s %s %s %s %s" tabindex="-1" role="button">
                    <span class="%s">
                    <span class="%s %s">&#9650;</span>
                    </span>
                    </a>'
                        , $id
                        , Css::ICONEDIT_BUTTON
                        , Css::ICONEDIT
                        , $params[Element\IconEdit::PARAM_POSITION]
                        , Css::CORNER_TL
                        , Css::CORNER_BL
                        , Css::BUTTON
                        , Css::WIDGET
                        , Css::STATE_DEFAULT
                        , Css::BUTTON_TEXT_ONLY
                        , Css::BUTTON_TEXT
                        , Css::ICON
                        , $params[Element\IconEdit::PARAM_ICON]);
                $html = sprintf($container, $icon, $input);
            }
        } else {
            $html = sprintf($container, $input, '');
        }
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/edit/edit.css');
        
        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["ed"]["%s"] = new edit("%s",%s);', $id, $id, $params);
        
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
