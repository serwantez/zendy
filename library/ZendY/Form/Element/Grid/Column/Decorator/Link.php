<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element\Grid\Column\Decorator;

use ZendY\Exception;

/**
 * Dekoruje kolumnę zawierającą wartości w postaci linków html
 * 
 * @author Piotr Zając
 */
class Link extends Custom {
    /*
     * Dekoruje kolumnę do wyświetlania linków
     * 
     * @return void
     */

    public function decorate() {
        if (!isset($this->_options['link'])) {
            throw new Exception('A valid link must be supplied.');
        }

        if (isset($this->_options['column']) && !is_array($this->_options['column'])) {
            $this->_options['column'] = array(
                $this->_options['column']
            );
        }
    }

    /**
     * Tworzy link zawierający wartość kolumny
     * 
     * @param array $row
     * @return string
     */
    public function cellValue(array $row) {

        // Count the number of arguments to be formatted
        $countArg = substr_count($this->_options['link'], '%');
        $link = $row[$this->getName()];

        if ($countArg > 0) {
            // If no columns have been supplied, format link using current column names
            if (!array_key_exists('column', $this->_options) || count($this->_options['column']) == 0) {
                $column = array_fill(1, $countArg, $row[$this->_column->getName()]);
            } else {
                // If columns have been defined, format link using user defined column names
                $column = array_intersect_key($row, array_flip($this->_options['column']));
            }

            $link = vsprintf($this->_options['link'], $column);
        }

        if (isset($this->_options['target']))
            $target = ' target="' . $this->_options['target'] . '"';
        else
            $target = '';
        return sprintf('<a href="%s"%s>%s</a>', $link, $target, $row[$this->getName()]);
    }

}