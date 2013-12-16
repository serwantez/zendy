<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * Pomocnik do wygenerowania kontenera Box.
 * Kontener zawiera część nagłówkową i główną.
 * 
 * @author Piotr Zając
 */
class BoxContainer extends \ZendX_JQuery_View_Helper_UiWidget {

    /**
     * Generuje kod kontenera Box
     * 
     * @param string $id
     * @param string $content
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function boxContainer($id, $content, $params = array(), $attribs = array()) {
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $id;
        }

        $html = '<div'
                . $this->_htmlAttribs($attribs)
                . '>'
                . sprintf('<div class="%s %s %s %s">'
                        , Css::DIALOG_TITLEBAR
                        , Css::WIDGET_HEADER
                        , Css::CORNER_ALL
                        , Css::HELPER_CLEARFIX)
                . sprintf('<span class="%s">', Css::DIALOG_TITLE)
                . $params['title']
                . '</span>
                    </div>'
                . sprintf('<div class="%s %s">'
                        , Css::DIALOG_CONTENT
                        , Css::WIDGET_CONTENT)
                . $content
                . '</div>
                    </div>';
        return $html;
    }

}