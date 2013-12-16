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
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
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
    public function setSource($value) {
        $this->setAttrib('src', $value);
        return $this;
    }

    /**
     * Zwraca ścieżkę pliku graficznego
     * 
     * @return string
     */
    public function getSource() {
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
