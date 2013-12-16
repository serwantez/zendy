<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

require_once "ZendX/JQuery/View/Helper/TabContainer.php";

/**
 * Pomocnik do wygenerowania kontenera zakładek
 */
class TabContainer extends \ZendX_JQuery_View_Helper_TabContainer {

    /**
     * Render TabsContainer with all the currently registered tabs.
     *
     * Render all tabs to the given $id. If no arguments are given the
     * tabsContainer view helper object is returned and can be used
     * for chaining {@link addPane()} for tab pane adding.
     *
     * @link   http://docs.jquery.com/UI/Tabs
     * @param  string|null $id
     * @param  array|null  $params
     * @param  array|null  $attribs
     * @return string|\ZendX_JQuery_View_Helper_TabsContainer
     */
    public function tabContainer($id = null, $params = array(), $attribs = array()) {
        if (func_num_args() === 0) {
            return $this;
        }

        if (!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }

        $jh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $content = "";
        if (isset($this->_tabs[$id])) {
            $list = '<ul class="' . Css::TABS_NAV . '">' . PHP_EOL;
            $html = '';
            foreach ($this->_tabs[$id] as $k => $v) {
                $frag_name = sprintf('%s', $v['options']['id']);
                $opts = $v['options'];
                if (isset($opts['contentUrl'])) {
                    $list .= '<li class="' . Css::TABS_NAV_ITEM . '"><a href="' .
                            $opts['contentUrl'] . '"><span>' .
                            $v['name'] . '</span></a></li>' . PHP_EOL;
                } else {
                    $list .= '<li class="' . Css::TABS_NAV_ITEM . '"><a href="#' .
                            $frag_name . '"><span>' .
                            $v['name'] . '</span></a></li>' . PHP_EOL;
                    $html .= '<div id="' . $frag_name .
                            '" class="' . Css::TABS_PANEL . '">' .
                            $v['content'] . '</div>' . PHP_EOL;
                }
            }
            $list .= '</ul>' . PHP_EOL;

            $content = $list . $html;
            unset($this->_tabs[$id]);
        }

        $jsh = array();
        if (array_key_exists(\ZendY\Form\Container\Tab::PARAM_HIDDENMAP, $params)) {
            $jsh[] = sprintf('%s("#%s").on("tabsactivate", function(event, ui) {', $jh, $attribs['id']);
            foreach ($params[\ZendY\Form\Container\Tab::PARAM_HIDDENMAP] as $mapData) {
                $jsh[] = sprintf('if (ui.newPanel.attr("id") == "%s") dc["mp"]["%s"].refresh();'
                        , $mapData['panel'], $mapData['map']);
            }
            $jsh[] = '});';
            unset($params[\ZendY\Form\Container\Tab::PARAM_HIDDENMAP]);
        }


        if (count($params)) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js[] = sprintf('%s("#%s").tabs(%s);', $jh, $attribs['id'], $params
        );
        $js = array_merge($js, $jsh);
        $this->jquery->addOnLoad(implode("\n", $js));
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/tabs/jquery.ui.tabs.css');

        $html = '<div'
                . $this->_htmlAttribs($attribs)
                . '>' . PHP_EOL
                . $content
                . '</div>' . PHP_EOL;
        return $html;
    }

}