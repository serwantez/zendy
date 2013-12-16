<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Widok pliku tekstowego
 *
 * @author Piotr Zając
 */
class TextFileView extends Widget {

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