<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania kontrolki hasła
 *
 * @author Piotr Zając
 */
class Password extends Widget {

    /**
     * Generuje kod kontrolki hasła
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function password($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</span>';

        $html = sprintf($container, $this->view->formPassword($id, $value, $attribs['inner']));
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/edit/edit.css');
        return $html;
    }

}

