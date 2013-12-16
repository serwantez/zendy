<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Css;

/**
 * Widok pliku tekstowego, którego ścieżka dostępu zapisana jest w zbiorze danych
 *
 * @author Piotr Zając
 */
class TextFileView extends Text {

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'textFileView';
        $this->addClasses(array(
            Css::TEXTFILEVIEW,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
    }

    /**
     * Formatuje wartość wyświetlaną przez kontrolkę
     * 
     * @param string $value
     * @return string
     */
    static public function formatValue($value) {
        if (is_file($value)) {
            $value = highlight_file($value, true);
        } else {
            $value = '';
        }
        return $value;
    }

}