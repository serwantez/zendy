<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Css;

/**
 * Widok pliku tekstowego z dokumentacją, którego ścieżka dostępu zapisana jest w zbiorze danych
 *
 * @author Piotr Zając
 */
class DocFileView extends Text {

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
        $this->helper = 'docFileView';
        $this->addClasses(array(
            Css::LONGTEXT,
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
            $value = file_get_contents($value);
            //dołączanie plików zewnętrznych
            $fileStart = '[file]';
            $fileEnd = '[/file]';
            while (($posStart = strpos($value, $fileStart)) !== false) {
                $posEnd = strpos($value, $fileEnd, $posStart);
                $file = substr($value, $posStart + strlen($fileStart), ($posEnd - $posStart - strlen($fileStart)));
                if (is_file($file)) {
                    $value = substr_replace($value, file_get_contents($file), $posStart, ($posEnd - $posStart + strlen($fileEnd)));
                } else {
                    $value = substr_replace($value, $file, $posStart, ($posEnd - $posStart + strlen($fileEnd)));
                }
            }
            //podświetlanie kodu
            $codeStart = '[code]';
            $codeEnd = '[/code]';
            while (($posStart = strpos($value, $codeStart)) !== false) {
                $posEnd = strpos($value, $codeEnd, $posStart);
                $code = substr($value, $posStart + strlen($codeStart), ($posEnd - $posStart - strlen($codeStart)));
                $value = substr_replace($value, highlight_string($code, true), $posStart, ($posEnd - $posStart + strlen($codeEnd)));
            }
        } else {
            $value = '';
        }
        return $value;
    }

}