<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Widok pliku tekstowego z dokumentacją
 *
 * @author Piotr Zając
 */
class DocFileView extends Widget {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_CODEHIGHLIGHT = 'codeHighlight';
    const PROPERTY_FILENAME = 'fileName';

    /**
     * Parametry
     */
    const PARAM_CODE_HIGHLIGHT = 'codeHighlight';

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
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_CODEHIGHLIGHT,
        self::PROPERTY_DISABLED,
        self::PROPERTY_FILENAME,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'docFileView';
        $this->addClasses(array(
            Css::LONGTEXT,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL,
            Css::DOCUMENT
        ));
        $this->setCodeHighlight(true);
    }

    /**
     * Ustawia parametr informujący, czy kod zawarty w pliku 
     * pomiędzy znacznikami [code] i [/code] ma być kolorowany
     * 
     * @param bool $highlight
     * @return \ZendY\Form\Element\DocFileView
     */
    public function setCodeHighlight($highlight = true) {
        $this->setJQueryParam(self::PARAM_CODE_HIGHLIGHT, $highlight);
        return $this;
    }

    /**
     * Informuje, czy kod zawarty w pliku 
     * pomiędzy znacznikami [code] i [/code] ma być kolorowany
     * 
     * @return bool
     */
    public function getCodeHighlight() {
        return $this->getJQueryParam(self::PARAM_CODE_HIGHLIGHT);
    }

    /**
     * Ustawia ścieżkę do pliku
     * 
     * @param string $fileName
     * @return \ZendY\Form\Element\DocFileView
     */
    public function setFileName($fileName) {
        $this->_fileName = $fileName;
        if (is_file($fileName)) {
            $value = file_get_contents($fileName);
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
            if ($this->getCodeHighlight()) {
                $codeStart = '[code]';
                $codeEnd = '[/code]';
                while (($posStart = strpos($value, $codeStart)) !== false) {
                    $posEnd = strpos($value, $codeEnd, $posStart);
                    $code = substr($value, $posStart + strlen($codeStart), ($posEnd - $posStart - strlen($codeStart)));
                    $value = substr_replace($value, highlight_string($code, true), $posStart, ($posEnd - $posStart + strlen($codeEnd)));
                }
            }
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