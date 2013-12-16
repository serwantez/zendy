<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania przycisków "radio"
 *
 * @author Piotr Zając
 */
class RadioButton extends Widget {

    /**
     * Generuje kod przycisków "radio".
     * 
     * @param string $name
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @param array|null $options
     * @param string|null $listsep
     * @return string
     */
    public function radioButton($name, $value = null, array $params = array()
    , $attribs = array(), $options = array(), $listsep = "<br />\n") {
        $html = array();
        if (count($params) > 0) {
            if (array_key_exists('text', $params)) {
                $text = $params['text'];
                unset($params['text']);
            }
            //przyporządkowanie ikon do pojedynczych przycisków
            if (array_key_exists('icons', $params)) {
                $icons = $params['icons'];
                unset($params['icons']);
            }
            $i = 0;
            foreach ($options as $key => $option) {
                if ($i == 0)
                    $suf = '';
                else
                    $suf = '-' . $key;
                if (isset($icons[$key]))
                    $btnParams['icons'] = $icons[$key];
                $btnParams['text'] = $text;
                $btnJsonParams = \ZendY\JQuery::encodeJson($btnParams);
                $js[] = sprintf('%s("#%s").button(%s);'
                        , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                        , $name . $suf
                        , $btnJsonParams);
                $i++;
            }
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = "{}";
        }

        $js[] = sprintf('%s("#%s-container").buttonset(%s);'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $name
                , $params);
        
        $js[] = sprintf('dc["rb"]["%s"] = new radiobutton("%s",%s);', $name, $name, $params); 

        $this->jquery->addJavascriptFile($this->view->host .
                '/library/components/radio/radiobutton.js');

        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html[] = '<script>' . implode(self::EOL, $js) . '</script>';
        } else {
            $this->jquery->addOnLoad(implode(self::EOL, $js));
        }

        $html[] = $this->view->radio($name, $value, array(), $attribs, $options, $listsep);

        return implode(self::EOL, $html);
    }

}
