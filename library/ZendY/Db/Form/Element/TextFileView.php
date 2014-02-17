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
        $this->helper = 'textFileView';
        $this->setClasses(array(
            Css::TEXTFILEVIEW,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('type', 'tfv');
        $this->setFrontEditParam('dataField', $this->getDataField());        
        $this->setFrontEditParam('attribs', $this->getAttribs());
        return $this->_frontEditParams;        
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