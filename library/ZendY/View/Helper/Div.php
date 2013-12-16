<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

require_once 'Zend/View/Helper/HtmlElement.php';

/**
 * Pomocnik do generowania prostych kontenerów div
 * 
 * @author Piotr Zając
 */
class Div extends \Zend_View_Helper_HtmlElement {

    /**
     * Generuje kod kontenera div
     * 
     * @param string $id
     * @param array|null $attribs
     * @param string|null $content
     * @return string
     */
    public function div($id, $attribs = null, $content = '') {
        $attribs = Widget::prepareCSS($attribs);
        $info = $this->_getInfo($id, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        $xhtml = '<div'
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs)
                . '>'
                . $content
                . '</div>';

        return $xhtml;
    }

    /**
     * Converts parameter arguments to an element info array.
     * 
     * @param string $id
     * @param array|null $attribs
     * @return array An element info array with keys for attribs.
     */
    protected function _getInfo($id, $attribs = null) {
        $info = array(
            'id' => $id,
            'attribs' => $attribs
        );

        $attribs = (array) $attribs;

        // Set ID for element
        if (array_key_exists('id', $attribs)) {
            $info['id'] = (string) $attribs['id'];
        }

        // Remove attribs that might overwrite the other keys. We do this LAST
        // because we needed the other attribs values earlier.
        foreach ($info as $key => $val) {
            if (array_key_exists($key, $attribs)) {
                unset($attribs[$key]);
            }
        }
        $info['attribs'] = $attribs;

        return $info;
    }

}
