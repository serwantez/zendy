<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Form\Element;
use ZendY\Css;

/**
 * Pomocnik do wygenerowania listy rozwijalnej z ikoną IconComboBox
 * 
 * @author Piotr Zając
 */
class IconCombobox extends Widget {

    /**
     * Generuje kod listy rozwijalnej z ikoną IconComboBox
     * 
     * @param string $name
     * @param mixed|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @param array|null $options
     * @param string|null $listsep
     * @return string
     */
    public function iconCombobox($name, $value = null, $params = null, $attribs = null, $options = null, $listsep = "<br />\n") {

        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info);
        $params = $this->_prepareParams($name, $params);
        $value = array_map('strval', (array) $value);
        $jqh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();
        if (array_key_exists(Element\IconCombobox::PARAM_ICON, $params))
            $icon = $params[Element\IconCombobox::PARAM_ICON];
        else
            $icon = Css::ICON_TRIANGLE1S;

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js[] = sprintf('dc["ic"]["%s"] = new iconComboBox("%s",%s);', $id, $id, $params);

        $attribs = $this->_extractAttributes($attribs);

        //pole ukryte
        $hidden = $this->view->formHidden($id) . self::EOL;

        //kontener
        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s%s</div>%s';

        //lista
        $list[] = sprintf('<ul'
                . ' id="%s-list"'
                . ' class="%s %s %s %s %s"'
                . ' style="display: none; width: 200px; max-height: 300px; overflow: auto;">'
                , $this->view->escape($id)
                , Css::MENU
                , Css::WIDGET
                , Css::WIDGET_CONTENT
                , Css::CORNER_ALL
                , Css::MENU_ICONS);
        foreach ($options as $opt_value => $opt_label) {
            $list[] = $this->_buildLi($opt_value, $opt_label, $value);
        }
        $list[] = '</ul>';

        //pole tekstowe
        $text = $this->view->formText($id . '-text', '', $attribs['inner']) . self::EOL;

        //ikona
        $iconButton = sprintf('<a id="%s-button" 
            class="%s %s %s %s %s %s %s" tabindex="-1" role="button">
            <span class="%s">
            <span class="%s %s">&#9650;</span>
                </span>
                </a>'
                        , $id
                        , Css::ICONEDIT_BUTTON
                        , Css::ICONEDIT_RIGHT_BUTTON
                        , Css::CORNER_RIGHT
                        , Css::BUTTON
                        , Css::WIDGET
                        , Css::STATE_DEFAULT
                        , Css::BUTTON_TEXT_ONLY
                        , Css::BUTTON_TEXT
                        , Css::ICON
                        , $icon) . self::EOL;

        //złączenie elementów
        $html = sprintf($container, $text, $iconButton, $hidden, implode(self::EOL, $list));
        
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/edit/edit.css');
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/menu/jquery.ui.menu.css');
        $this->jquery->addJavascriptFile($this->view->host .
                '/library/components/iconcombobox/iconcombobox.js');
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>'. implode(self::EOL, $js) . '</script>';
        } else {
            $this->jquery->addOnLoad(implode(self::EOL, $js));
        }        
        return $html;
    }

    /**
     * Generuje element listy - znacznik li
     * 
     * @param mixed $value
     * @param string $label
     * @param array $selected
     * @return string
     */
    protected function _buildLi($value, $label, array $selected) {
        $li = sprintf('<li key="%s" class="%s'
                , $this->view->escape($value)
                , Css::MENU_ITEM)
        ;
        // selected?
        if (in_array((string) $value, $selected)) {
            $li .= ' ' . Css::STATE_ACTIVE;
        }

        $li .= sprintf('"><a href="#" class="%s">'
                        , Css::CORNER_ALL) . self::EOL;
        $li .= sprintf('<span class="%s %s"></span>'
                        , Css::ICON, $value) . self::EOL;
        $li .= $this->view->escape($label);
        $li .= '</a></li>' . self::EOL;
        return $li;
    }

}