<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;

/**
 * Pomocnik do wygenerowania kontrolki TextFileView
 * wyświetlającej zawartość pliku tekstowego
 *
 * @author Piotr Zając
 */
class TextFileView extends Widget {

    /**
     * Generuje kod kontrolki TextFileView
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function textFileView($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</div>';

        $rowCount = substr_count($value, '<br />');
        $numbers = '';
        if ($rowCount > 0) {
            for ($i = 0; $i <= $rowCount; $i++) {
                $numbers .= ($i + 1) . '<br />';
            }
        }
        $viewer = sprintf('<div class="%s">
            <div class="%s" id="%s"><pre>%s</pre></div>
            <div class="%s %s" id="%s"><pre>%s</pre></div>
            </div>'
                , Css::TEXTFILEVIEW_CONTAINER
                , Css::WIDGET_HEADER . ' ' . Css::TEXTFILEVIEW_ROWNUMBERS
                , $id . 'RowNumbers'
                , $numbers
                , Css::WIDGET_CONTENT
                , Css::TEXTFILEVIEW_CONTENT
                , $id
                , $value);
        $html = sprintf($container, $viewer);
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/text/text.css');

        return $html;
    }

}