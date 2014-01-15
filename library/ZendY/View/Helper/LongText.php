<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania kontrolki tekstowej LongText
 *
 * @author Piotr Zając
 */
class LongText extends Widget {

    /**
     * Generuje kod kontrolki tekstowej LongText
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function longText($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</div>';
        $text = sprintf('<div id="%s">%s</div>', $id, $value);
        $html = sprintf($container, $text);
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/text/text.css');

        return $html;
    }

}

