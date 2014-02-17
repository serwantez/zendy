<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;
use ZendY\Exception;

/**
 * Widok pliku tekstowego
 *
 * @author Piotr Zając
 */
class TextFileView extends Widget {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_FILENAME = 'fileName';
    
    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_FILENAME,
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
     * Ścieżka dostępu do pliku
     * 
     * @var string 
     */
    protected $_fileName;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'textFileView';
        $this->addClasses(array(
            Css::TEXTFILEVIEW,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
    }
    
    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setDisabled($disabled) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }
    
    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setReadOnly($readOnly) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }    

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setRequired($flag = true) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }
    

    /**
     * Ustawia ścieżkę do pliku
     * 
     * @param string $fileName
     * @return \ZendY\Form\Element\TextFileView
     */
    public function setFileName($fileName) {
        $this->_fileName = $fileName;
        if (is_file($fileName)) {
            $value = highlight_file($fileName, true);
        } else {
            $value = '';
        }
        $this->setValue($value);
        return $this;
    }

    /**
     * Zwraca ścieżkę do pliku
     * 
     * @return string
     */
    public function getFileName() {
        return $this->_fileName;
    }

}