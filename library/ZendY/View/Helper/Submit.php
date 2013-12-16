<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

/**
 * Pomocnik do wygenerowania przycisku Submit
 *
 * @author Piotr Zając
 */
class Submit extends CustomButton {

    /**
     * Generuje przycisk Submit
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function submit($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $js[] = $this->_prepareShortKey($attribs['id'], $params);

        //usunięcie klasy ui-button
        $jsi[] = sprintf('%s("#%s").removeClass("%s");'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $attribs['id']
                , Css::BUTTON);

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = "{}";
        }

        $js[] = sprintf('%s("#%s").button(%s);'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $attribs['id'], $params);
        if (isset($jsi))
            $js = array_merge($js, $jsi);
        $this->view->jQuery()->addOnLoad(implode("\n", $js));
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/submit/submit.css');

        return $this->view->formSubmit($id, $value, $attribs);
    }

}
