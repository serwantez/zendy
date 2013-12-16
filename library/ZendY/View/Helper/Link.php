<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania linku na formularzu
 *
 * @author Piotr Zając
 */
class Link extends Widget {

    /**
     * Generuje kod linku
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function link($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/link/link.css');
        $xhtml = $this->view->formHidden('_' . $attribs['name'], $attribs['value']);
        $xhtml .= '<a'
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs)
                . '>'
                . $value
                . '</a>';

        return $xhtml;
    }

}
