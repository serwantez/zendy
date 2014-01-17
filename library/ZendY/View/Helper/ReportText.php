<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania kontrolki tekstowej raportu ReportText
 *
 * @author Piotr Zając
 */
class ReportText extends Widget {

    /**
     * Generuje kod kontrolki tekstowej raportu
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $attribs
     * @return string
     */
    public function reportText($id, $value = null, array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</div>';
        $text = sprintf('<span id="%s">%s</span>', $id, $value);
        $html = sprintf($container, $text);

        return $html;
    }

}

