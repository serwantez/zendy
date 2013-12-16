<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\System;
use ZendY\Css;

/**
 * Pomocnik do wygenerowania siatki danych (Grid)
 *
 * @author Piotr Zając
 */
class Grid extends Widget {

    /**
     * Generuje siatkę danych Grid
     * 
     * @param string $name
     * @param mixed|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @param array|null $options
     * @return string
     */
    public function grid($name, $value = null, $params = null, $attribs = null, $options = null) {
        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $info = $this->_getInfo($name, $value, $attribs, $options);
        extract($info);
        $params = $this->_prepareParams($name, $params);
        $value = array_map('strval', (array) $value);
        $jqh = \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        if (isset($params['firstColWidth']))
            $firstColWidth = $params['firstColWidth'];
        else
            $firstColWidth = 19;
        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js[] = sprintf('dc["gr"]["%s"] = new grid("%s",%s);', $id, $id, $params);

        $hidden = $this->view->formHidden($id) . self::EOL;

        $columns = $attribs['columns'];
        unset($attribs['columns']);
        $attribs = $this->_extractAttributes($attribs);

        $container = '<div '
                . ' id="' . $this->view->escape($id) . '-grid"'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s</div>';

        $keyColumns = array();
        $colgroup[] = '<colgroup>';
        $borderWidth = 1;
        $padding = 2;
        $gridWidthUnit = 'px';
        //pierwsza kolumna
        $gridWidth = $firstColWidth;
        $colgroup[] = sprintf('<col width="%s" style="min-width: %s;" />'
                , $firstColWidth
                , $firstColWidth
        );
        $th[] = sprintf('<th class="%s" style="width: %s;">
                </th>'
                , Css::STATE_DEFAULT
                , $firstColWidth
        );
        $td0[] = sprintf('<td style="height: 0px; width: %s; min-width: %s;"></td>'
                , $firstColWidth
                , $firstColWidth
        );
        foreach ($columns as $key => $column) {
            if ($column->isKey) {
                $keyColumns[] = $key;
            }
            $width = $column->getWidth();
            $widthWithUnit = self::implodeArrayProperty($width);
            $colgroup[] = sprintf('<col width="%s" style="min-width: %s;" />', $widthWithUnit, $widthWithUnit);
            $alignHorizontal[$key] = $column->getAlign();
            $th[] = sprintf('<th id="%s" class="%s" style="width: %s;"><div class="%s">
                <span class="%s">%s</span>
                </div></th>'
                    , $id . '_' . $column->getId()
                    , Css::STATE_DEFAULT
                    , $widthWithUnit
                    , Css::GRID_HEADER_CONTAINER
                    , Css::GRID_HEADER_LABEL
                    , $column->getLabel());
            $td0[] = sprintf('<td style="height: 0px; width: %s; min-width: %s;"></td>'
                    , $widthWithUnit
                    , $widthWithUnit);
            $gridWidth += $width['value'] + 2 * $padding + $borderWidth;
            $gridWidthUnit = $width['unit'];
        }
        $colgroup[] = '</colgroup>';
        $grid[] = sprintf('<div class="%s">', Css::GRID_HEADERBODY);
        $grid[] = sprintf('<div class="%s %s %s" style="min-width: %s"><div style="width: %s;">'
                , Css::STATE_DEFAULT
                , Css::GRID_HEADER
                , Css::CORNER_TOP
                , ($gridWidth + System::getParam(System::SCROLL_WIDTH)) . $gridWidthUnit
                , $gridWidth . $gridWidthUnit);
        $grid[] = sprintf('<table cellspacing="0" cellpadding="0" border="0" style="width: %s;">', $gridWidth . $gridWidthUnit);

        $grid = array_merge($grid, $colgroup);
        $grid[] = '<thead>';
        $grid[] = '<tr>';
        $grid = array_merge($grid, $th);
        $grid[] = '</tr>';
        $grid[] = '</thead>';
        $grid[] = '</table>';
        $grid[] = '</div></div>';
        $grid[] = sprintf('<div class="%s" style="min-width: %s;"><div style="width: %s">'
                , Css::GRID_BODY
                , ($gridWidth + System::getParam(System::SCROLL_WIDTH)) . $gridWidthUnit
                , $gridWidth . $gridWidthUnit);
        $grid[] = sprintf('<table cellspacing="0" cellpadding="0" border="0" style="width: %s;">', $gridWidth . $gridWidthUnit);
        $grid = array_merge($grid, $colgroup);
        $grid[] = '<tbody>';

        $tr[] = sprintf('<tr class="%s %s">%s</tr>'
                , Css::WIDGET_CONTENT
                , Css::GRID_FIRSTROW
                , implode('', $td0));
        foreach ($options as $row) {
            $id = array();
            foreach ($keyColumns as $keyColumn) {
                $id[] = $row[$keyColumn];
            }
            $tr[] = sprintf('<tr class="%s" key="%s">'
                    , Css::WIDGET_CONTENT
                    , implode(';', $id));
            $tr[] = sprintf('<td class="%s"></td>', Css::STATE_DEFAULT);
            foreach ($columns as $key => $column) {
                $tr[] = sprintf('<td class="%s">%s</td>', $alignHorizontal[$key], $column->cellValue($row));
            }
            $tr[] = '</tr>';
        }
        $grid = array_merge($grid, $tr);
        $grid[] = '</tbody>';
        $grid[] = '</table>';
        $grid[] = '</div>';
        $grid[] = '</div>';
        $grid[] = '</div>';

        // Ładuje zmienne widoku wtyczki
        $grid = array_merge($grid, $this->view->gridPluginBroker['html']);
        $js = array_merge($js, $this->view->gridPluginBroker['onload']);

        $html = sprintf($container, implode(self::EOL, $grid), $hidden);
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/grid/grid.js');
        $this->jquery->addOnLoad(implode(self::EOL, $js));
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/grid/grid.css');
        return $html;
    }

}

