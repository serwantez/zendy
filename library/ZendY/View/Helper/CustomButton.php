<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Form\Element;

/**
 * Klasa bazowa dla pomocników widoku przycisku
 *
 * @author Piotr Zając
 */
abstract class CustomButton extends Widget {

    /**
     * Generuje kod JS odpowiedzialny za uruchomienie skrótów klawiaturowych
     * 
     * @param string $id
     * @param array $params
     * @return string
     */
    protected function _prepareShortKey($id, &$params) {
        $jss = '';
        if (array_key_exists(Element\CustomButton::PARAM_SHORTKEY, $params)
                && isset($params[Element\CustomButton::PARAM_SHORTKEY])) {
            $params['label'] = $params['label'] . ' [' . $params[Element\CustomButton::PARAM_SHORTKEY] . ']';

            $shortKeyParams = array(
                'type' => 'keydown',
                'propagate' => 0,
                'target' => new \Zend_Json_Expr('document')
            );
            $shortKeyParams = \ZendY\JQuery::encodeJson($shortKeyParams);
            $fn = new \Zend_Json_Expr(sprintf('function() {
                $("#%s").trigger("click");
                }', $id));
            $jss = sprintf('shortcut.add("%s",%s,%s);', $params[Element\CustomButton::PARAM_SHORTKEY], $fn, $shortKeyParams);
        }
        unset($params[Element\CustomButton::PARAM_SHORTKEY]);
        $this->jquery->addJavascriptFile($this->view->host . '/library/shortcut/shortcut.js');
        return $jss;
    }

}
