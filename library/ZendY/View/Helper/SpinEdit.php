<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania kontrolki numerycznej SpinEdit
 *
 * @author Piotr Zając
 */
class SpinEdit extends Widget {

    /**
     * Generuje kod pola tekstowe Edit dla wartości numerycznych
     *
     * @param  string $id
     * @param  string|null $value
     * @param  array|null  $params
     * @param  array|null  $attribs
     * @return string
     */
    public function spinEdit($id, $value = "", array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        if (count($params)) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js[] = sprintf('dc["se"]["%s"] = new spinedit("%s",%s);', $id, $id, $params);

        $html = $this->view->formText($id, $value, $attribs);
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/spinner/jquery.ui.spinner.css');

        $this->jquery->addJavascriptFile($this->view->host .
                '/library/components/spinner/spinedit.js');
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>' . implode(self::EOL, $js) . '</script>';
        } else {
            $this->jquery->addOnLoad(implode(self::EOL, $js));
        }
        return $html;
    }

}