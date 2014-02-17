<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Css;

/**
 * Kontrolka do odczytywania długich wartości tekstowych
 *
 * @author Piotr Zając
 */
class LongText extends Text {

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_DATAFIELD,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'longText';
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
        $codeStart = '[code]';
        $codeEnd = '[/code]';
        while (($posStart = strpos($value, $codeStart)) !== false) {
            $posEnd = strpos($value, $codeEnd, $posStart);
            $code = substr($value, $posStart + strlen($codeStart), ($posEnd - $posStart - strlen($codeStart)));
            $value = substr_replace($value, highlight_string($code, true), $posStart, ($posEnd - $posStart + strlen($codeEnd)));
        }
        return $value;
    }

}
