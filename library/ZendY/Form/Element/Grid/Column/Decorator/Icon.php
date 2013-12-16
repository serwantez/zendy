<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element\Grid\Column\Decorator;

/**
 * Dekoruje kolumnę zawierającą wartości w postaci obrazka
 * 
 * @author Piotr Zając
 */
class Icon extends Custom {
    /*
     * Dekoruje kolumnę do wyświetlania ikon
     * 
     * @return void
     */

    public function decorate() {
        
    }

    /**
     * Tworzy ikonę wyświetlającą wartość kolumny
     * 
     * @param array $row
     * @return mixed
     */
    public function cellValue(array $row) {
        $icon = $row[$this->getName()];
        if (isset($this->_options['icons']) && array_key_exists($icon, $this->_options['icons'])) {
            $icon = $this->_options['icons'][$icon];
        }

        return sprintf('<span class="%s %s" style="display: inline-block;"></span>'
                        , \ZendY\Css::ICON
                        , $icon);
    }

}