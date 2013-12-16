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
class Button extends CustomButton {

    /**
     * Generuje kod przycisku
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function button($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $js[] = $this->_prepareShortKey($attribs['id'], $params, $attribs);

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = "{}";
        }

        $js[] = sprintf('%s("#%s").button(%s);', \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(), $attribs['id'], $params);
        $this->view->jQuery()->addOnLoad(implode("\n", $js));

        return $this->view->formButton($id, $value, $attribs);
    }

}
