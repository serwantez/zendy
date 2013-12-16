<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

/**
 * Pomocnik do wygenerowania listy rozwiniętej ListBox
 * 
 * @author Piotr Zając
 */
class Listbox extends Widget {

    /**
     * Generuje kod listy rozwiniętej ListBox
     * 
     * @param string $name
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @param array|null $options
     * @param string|null $listsep
     * @return string
     */
    public function listbox($name, $value = null, $params = null, $attribs = null, $options = null, $listsep = "<br />\n") {

        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info);
        $params = $this->_prepareParams($name, $params);
        $value = array_map('strval', (array) $value);
        $jqh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["lb"]["%s"] = new listBox("%s",%s);', $id, $id, $params);

        $hidden = $this->view->formHidden($id) . self::EOL;

        $attribs = $this->_extractAttributes($attribs);
        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s</span>';

        $list[] = '<ul'
                . ' id="' . $this->view->escape($id) . '-list"'
                . $this->_htmlAttribs($attribs['inner'])
                . '>';
        foreach ($options as $opt_value => $opt_label) {
            $list[] = $this->_buildLi($opt_value, $opt_label, $value);
        }
        $list[] = '</ul>';

        $html = sprintf($container, implode(self::EOL, $list), $hidden);

        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/listbox/listbox.css');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/listbox/listbox.js');
        
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
     * @param string $label
     * @param array $selected
     * @return string
     */
    protected function _buildLi($value, $label, array $selected) {
        $li = '<li'
                . ' key="' . $this->view->escape($value) . '"'
                . ' class="'
        ;
        // selected?
        if (in_array((string) $value, $selected)) {
            $li .= ' ' . Css::STATE_ACTIVE;
        }

        $li .= '"><a href="#">' . $this->view->escape($label) . '</a></li>';
        return $li;
    }

}