<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania wstęgi raportu - ReportBand
 *
 * @author Piotr Zając
 */
class ReportBand extends Widget {

    /**
     * Generuje kod wstęgi raportu
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $attribs
     * @param array|null $options
     * @return string
     */
    public function reportBand($id, $value = null, array $attribs = array(), $options = null) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        $columns = $attribs['columns'];
        unset($attribs['columns']);

        $th = array();
        foreach ($columns as $key => $column) {
            if ($column->isKey) {
                $keyColumns[] = $key;
            }
            $alignHorizontal[$key] = $column->getAlign();
            $th[] = sprintf('<th id="%s">%s</th>'
                    , $id . '_' . $column->getId()
                    , $column->getLabel());
        }

        $xhtml[] = '<table'
                . $this->_htmlAttribs($attribs)
                . '>';
        $xhtml[] = '<thead>';
        $xhtml[] = '<tr>';
        $xhtml = array_merge($xhtml, $th);
        $xhtml[] = '</tr>';
        $xhtml[] = '</thead>';
        $xhtml[] = '<tbody>';
        $tr = array();
        foreach ($options as $row) {
            $tr[] = '<tr>';
            foreach ($columns as $key => $column) {
                $tr[] = sprintf('<td class="%s">%s</td>', $alignHorizontal[$key], $column->cellValue($row));
            }
            $tr[] = '</tr>';
        }
        $xhtml = array_merge($xhtml, $tr);
        $xhtml[] = '</tbody>';
        $xhtml[] = '</table>';

        return implode(PHP_EOL, $xhtml);
    }

}

