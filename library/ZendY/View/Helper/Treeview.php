<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

/**
 * Pomocnik do wygenerowania kontrolki drzewa Treeview
 * 
 * @author Piotr Zając
 */
class Treeview extends Widget {

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
    public function treeview($name, $value = null, $params = null, $attribs = null, $options = null, $listsep = "<br />\n") {
        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info);
        $params = $this->_prepareParams($name, $params);
        $value = array_map('strval', (array) $value);
        $jqh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $icons = $params['icons'];

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["tv"]["%s"] = new treeview("%s",%s);', $id, $id, $params);

        $hidden = $this->view->formHidden($id) . self::EOL;

        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s</div>';

        $attribs['inner']['class'] = Css::TREE;
        $list[] = '<ul'
                . ' id="' . $this->view->escape($id) . '-list"'
                . $this->_htmlAttribs($attribs['inner'])
                . '>';
        foreach ($options as $opt_value => $option) {
            $list[] = $this->_buildLi($opt_value, $option, $value, $icons);
        }
        $list[] = '</ul>';

        $html = sprintf($container, implode(self::EOL, $list), $hidden);

        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/treeview/treeview.css');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/treeview/treeview.js');

        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>' . $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }

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
    protected function _buildLi($value, $option, array $selected, array $icons) {
        $li = '<li';
        if (isset($option['children'])) {
            $li .= ' aria-expanded="true"';
        }
        $li .= ' class="' . Css::TREE_NODE;
        if (!isset($option['children'])) {
            $li .= ' ' . Css::TREE_LEAF;
        }
        $li .= '">' . self::EOL;
        $li .= '<a'
                . ' key="' . $this->view->escape($value) . '"'
                . ' class="' . Css::CORNER_ALL . ' ' . Css::TREE_NODE_ICON;
        // selected?
        if (in_array((string) $value, $selected)) {
            $li .= ' ' . Css::STATE_ACTIVE;
        }
        $li .= '" href="#">';
        if (isset($option['icon'])) {
            $icon = $option['icon'];
        } elseif (isset($option['children'])) {
            $icon = $icons['nodeExpanded'];
        } else {
            $icon = $icons['leaf'];
        }
        $li .= '<span class="' . Css::ICON . ' ' . $icon . '"></span>' . self::EOL;
        $li .= $this->view->escape($option['label']) . '</a>';
        if (isset($option['children'])) {
            $li .= '<ul style="display: block;">';
            foreach ($option['children'] as $ch_value => $children) {
                $li .= $this->_buildLi($ch_value, $children, $selected, $icons);
            }
            $li .= '</ul>' . self::EOL;
        }
        if (isset($option['children'])) {
            $li .= '<span class="' . Css::TREE_NODE_HANDLE . ' ' . Css::ICON . ' ' . $icons['handleExpanded'] . '"></span>';
        }
        $li .= '</li>' . self::EOL;
        return $li;
    }

}