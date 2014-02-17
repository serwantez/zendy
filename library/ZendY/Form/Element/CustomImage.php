<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka obrazu
 *
 * @author Piotr Zając
 */
abstract class CustomImage extends Widget {
    /**
     * parametry
     */

    const PARAM_UPLOAD_DIRECTORY = 'uploadDirectory';
    const PARAM_NULL_PATH = 'nullPath';

    /**
     * metody
     */
    const PARAM_METHOD_LOAD = 'load';

    /**
     * Właściwości komponentu
     */
    const PROPERTY_ALT = 'alt';
    const PROPERTY_FILENAME = 'fileName';
    const PROPERTY_FIT = 'fit';
    const PROPERTY_NULLPATH = 'nullPath';
    const PROPERTY_UPLOADDIRECTORY = 'uploadDirectory';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_ALT,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_FILENAME,
        self::PROPERTY_FIT,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
        self::PROPERTY_NULLPATH,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_UPLOADDIRECTORY,
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
        $this->helper = 'image';
        $this->addClasses(array(
            Css::IMAGE,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setUploadDirectory($this->getView()->baseUrl() . '/' . \Blueimp\Upload\Handler::$uploadDir);
    }

    /**
     * Ustawia ścieżkę pliku graficznego
     * 
     * @param string $value
     * @return \ZendY\Form\Element\CustomImage
     */
    public function setFileName($value) {
        $this->setAttrib('src', $value);
        return $this;
    }

    /**
     * Zwraca ścieżkę pliku graficznego
     * 
     * @return string
     */
    public function getFileName() {
        return $this->getAttrib('src');
    }

    /**
     * Ustawia atrybut z podpisem obrazu
     * 
     * @param string $value
     * @return \ZendY\Form\Element\CustomImage
     */
    public function setAlt($value) {
        $this->setAttrib('alt', $value);
        return $this;
    }

    /**
     * Zwraca atrybut z podpisem obrazu
     * 
     * @return string
     */
    public function getAlt() {
        return $this->getAttrib('alt');
    }

    /**
     * Ustawia czy obraz ma być dopasowany do kontenera
     * 
     * @param type $fit
     * @return \ZendY\Form\Element\CustomImage
     */
    public function setFit($fit = TRUE) {
        if ($fit)
            $this->addClass(Css::IMAGE_FIT);
        else
            $this->removeClass(Css::IMAGE_FIT);
        return $this;
    }

    /**
     * Informuje czy obraz ma być dostosowany do kontenera
     * 
     * @return bool
     */
    public function getFit() {
        return $this->hasClass(Css::IMAGE_FIT);
    }

    /**
     * Ustawia katalog pobierania obrazu
     * 
     * @param string $directory
     * @return \ZendY\Form\Element\CustomImage
     */
    public function setUploadDirectory($directory) {
        $this->setJQueryParam(self::PARAM_UPLOAD_DIRECTORY, $directory);
        return $this;
    }

    /**
     * Zwraca katalog pobierania obrazu
     * 
     * @return string
     */
    public function getUploadDirectory() {
        return $this->getJQueryParam(self::PARAM_UPLOAD_DIRECTORY);
    }

    /**
     * Ustawia ścieżkę pustej grafiki
     * 
     * @param string $path
     * @return \ZendY\Form\Element\CustomImage
     */
    public function setNullPath($path) {
        $this->setJQueryParam(self::PARAM_NULL_PATH, $path);
        return $this;
    }

    /**
     * Zwraca ścieżkę pustej grafiki
     * 
     * @return string
     */
    public function getNullPath() {
        return $this->getJQueryParam(self::PARAM_NULL_PATH);
    }

}
