<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Decorator;

use ZendY\Report;
use ZendY\Report\Element;

/**
 * Renderuje wszystkie elementy zarejestrowane na bieżącym raporcie
 *
 */
class ReportElements extends Base {

    /**
     * Merges given two belongsTo (array notation) strings
     *
     * @param  string $baseBelongsTo
     * @param  string $belongsTo
     * @return string
     */
    public function mergeBelongsTo($baseBelongsTo, $belongsTo) {
        $endOfArrayName = strpos($belongsTo, '[');

        if ($endOfArrayName === false) {
            return $baseBelongsTo . '[' . $belongsTo . ']';
        }

        $arrayName = substr($belongsTo, 0, $endOfArrayName);

        return $baseBelongsTo . '[' . $arrayName . ']' . substr($belongsTo, $endOfArrayName);
    }

    /**
     * Renderuje elementy raportu
     *
     * @param  string $content
     * @return string
     */
    public function render($content) {
        $report = $this->getElement();
        if (!$report instanceof Report) {
            return $content;
        }

        $belongsTo = ($report instanceof Report) ? $report->getElementsBelongTo() : null;
        $elementContent = '';
        $separator = $this->getSeparator();
        $translator = $report->getTranslator();
        $items = array();
        $view = $report->getView();
        //echo get_class($report);
        foreach ($report as $item) {
            $item->setView($view)
                    ->setTranslator($translator);
            if ($item instanceof Element) {
                $item->setBelongsTo($belongsTo);
            } elseif (!empty($belongsTo) && ($item instanceof Report)) {
                if ($item->isArray()) {
                    $name = $this->mergeBelongsTo($belongsTo, $item->getElementsBelongTo());
                    $item->setElementsBelongTo($name, true);
                } else {
                    $item->setElementsBelongTo($belongsTo, true);
                }
            }

            $items[] = $item->render();
        }
        $elementContent = implode($separator, $items);

        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $elementContent . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $elementContent;
        }
    }

}
