<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

require_once "ZendX/JQuery/View/Helper/AccordionContainer.php";

/**
 * Pomocnik do wygenerowania kontenera zakładek harmonijkowych
 */
class AccordionContainer extends \ZendX_JQuery_View_Helper_AccordionContainer {

    /**
     * Render Accordion with the currently registered elements.
     *
     * If no arguments are given, the accordion object is returned so that
     * chaining the {@link addPane()} function allows to register new elements
     * for an accordion.
     *
     * @link   http://docs.jquery.com/UI/Accordion
     * @param  string $id
     * @param  array  $params
     * @param  array  $attribs
     * @return string|ZendY\View\Helper\AccordionContainer
     */
    public function accordionContainer($id = null, array $params = array(), array $attribs = array()) {
        if (0 === func_num_args()) {
            return $this;
        }

        if (!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }
        
        $jh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $html = "";
        if (isset($this->_panes[$id])) {
            foreach ($this->_panes[$id] AS $element) {
                $html .= sprintf($this->getElementHtmlTemplate(), $element['name'], $element['content']) . PHP_EOL;
            }

            $jsh = array();
            if (array_key_exists(\ZendY\Form\Container\Accordion::PARAM_HIDDENMAP, $params)) {
                $jsh[] = sprintf('%s("#%s").on("accordionactivate", function(event, ui) {', $jh, $attribs['id']);
                foreach ($params[\ZendY\Form\Container\Accordion::PARAM_HIDDENMAP] as $mapData) {
                    $jsh[] = sprintf('if (ui.newPanel.attr("id") == "%s") dc["mp"]["%s"].refresh();'
                            , $mapData['panel'], $mapData['map']);
                }
                $jsh[] = '});';
                unset($params[\ZendY\Form\Container\Tab::PARAM_HIDDENMAP]);
            }

            if (count($params) > 0) {
                $params = \ZendY\JQuery::encodeJson($params);
            } else {
                $params = "{}";
            }

            $js[] = sprintf('%s("#%s").accordion(%s);', $jh, $attribs['id'], $params
            );
            $js = array_merge($js, $jsh);
            $this->jquery->addOnLoad(implode(self::EOL, $js));
            $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/accordion/jquery.ui.accordion.css');

            $html = $this->getAccordionTemplate($attribs, $html);
        }
        return $html;
    }

}