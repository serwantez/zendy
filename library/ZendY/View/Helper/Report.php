<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania raportu
 *
 * @author Piotr Zając
 */
class Report extends ReportElement {

    /**
     * Generuje kod raportu
     * 
     * @param string $id
     * @param array|null $attribs
     * @param string|null $content
     * @return string
     */
    public function report($id, $attribs = null, $content = '') {
        $attribs = Widget::prepareCSS($attribs);
        $info = $this->_getInfo($id, $content, $attribs);
        extract($info);
        $xhtml[] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
        $xhtml[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $xhtml[] = '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >';
        $xhtml[] = '<head>';
        $xhtml[] = sprintf('<link href="%s/library/components/report/report.css" rel="stylesheet" type="text/css" />', $this->view->host);
        $xhtml[] = '<title>' . $id . '</title>';
        $xhtml[] = '</head>';
        $xhtml[] = '<body>';
        $xhtml[] = '<div'
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs)
                . '>'
                . $content
                . '</div>';
        $xhtml[] = '</body>';
        $xhtml[] = '</html>';

        return implode(PHP_EOL, $xhtml);
    }

}
