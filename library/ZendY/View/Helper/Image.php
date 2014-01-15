<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania kontrolki graficznej Image
 *
 * @author Piotr Zając
 */
class Image extends Widget {

    /**
     * Generuje kod kontrolki graficznej Image
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function image($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<a'
                . ' href = "#"'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</a>';

        $image = '<img id="' . $id . '"';
        $image .= $this->_htmlAttribs($attribs['inner']);
        $image .= $this->getClosingBracket();
        $html = sprintf($container, $image);
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/image/image.css');

        return $html;
    }

}

