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
 * Pomocnik do wygenerowania menu podręcznego
 *
 * @author Piotr Zając
 */
class ContextMenu extends Widget {

    /**
     * Generuje kod kontrolki Treeview
     * 
     * @param string $name
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @param array|null $options
     * @param string|null $listsep
     * @return string
     */
    public function contextMenu($name, $value = null, $params = null, $attribs = null, $options = null, $listsep = "<br />\n") {
        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info);
        $params = $this->_prepareParams($name, $params);
        $value = array_map('strval', (array) $value);
        $jqh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        if (isset($params[Element\ContextMenu::PARAM_CONTEXT])) {
            $context = '"#' . $params[Element\ContextMenu::PARAM_CONTEXT] . '"';
            unset($params[Element\ContextMenu::PARAM_CONTEXT]);
        } else
            $context = 'document';

        //$icons = $params['icons'];

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js[] = sprintf('%s(%s).contextmenu(%s);'
                , $jqh, $context, $params);

        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/menu/jquery.ui.menu.css');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/jquery-ui-contextmenu-master/jquery.ui-contextmenu.min.js');
        $this->jquery->addOnLoad(implode(self::EOL, $js));

        $attribs = $this->_extractAttributes($attribs);
        $container = '<div'
                . ' id="' . $this->view->escape($id) . '-container"'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</div>';

        $attribs['inner']['class'] = Css::HELPER_HIDDEN;
        $list[] = '<ul'
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs['inner'])
                . '>';
        foreach ($options as $opt_value => $option) {
            $list[] = $this->_buildLi($id, $opt_value, $option, $value);
        }
        $list[] = '</ul>';

        //$html = sprintf($container, implode(self::EOL, $list));
        $html = implode(self::EOL, $list);
        return $html;
    }

    /**
     * Generuje kod elementu listy - znacznik li
     * 
     * @param mixed $value
     * @param array $option
     * @param array $selected
     * @param array $icons
     * @return string
     */
    protected function _buildLi($id, $value, $option, array $selected, array $icons = array()) {
        $li = '<li';
        $li .= '>' . self::EOL;
        $li .= '<a'
                . ' id="' . $this->view->escape($id) . '_' . $option['action'] . '"'
                . ' key="' . $this->view->escape($value) . '"';
        $li .= ' href="#">';
        if (isset($option['icon'])) {
            $li .= '<span class="' . Css::ICON . ' ' . $option['icon']['primary'] . '"></span>' . self::EOL;
        }
        $li .= $this->view->escape($option['label']) . '</a>';
        if (isset($option['children'])) {
            $li .= '<ul>';
            foreach ($option['children'] as $ch_value => $children) {
                $li .= $this->_buildLi($ch_value, $children, $selected, $icons);
            }
            $li .= '</ul>' . self::EOL;
        }
        $li .= '</li>' . self::EOL;
        return $li;
    }

}
