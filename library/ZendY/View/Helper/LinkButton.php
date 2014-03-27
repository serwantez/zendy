<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik go wygenerowania przycisku
 *
 * @author Piotr Zając
 */
class LinkButton extends CustomButton {

    /**
     * Generuje kod przycisku
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function linkButton($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = "{}";
        }

        $js[] = sprintf('%s("#%s").button(%s);', \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(), $attribs['id'], $params);
        $this->view->jQuery()->addOnLoad(implode("\n", $js));

        unset($attribs['value']);
        unset($attribs['options']);

        $container = '<span'
                . ' id="' . $this->view->escape($id) . '-container"'
                . '>%s</span>';
        //$hidden = $this->view->formHidden('_' . $id, $value);
        $a = '<a'
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs)
                . '>'
                . $value
                . '</a>';

        return sprintf($container, $a);
    }

}
