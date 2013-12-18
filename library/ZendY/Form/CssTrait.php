<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form;

use ZendY\Css;

/**
 * Cecha komponentów formatowanych przez klasy i style css
 *
 * @author Piotr Zając
 */
trait CssTrait {

    /**
     * Dodaje pojedynczą klasę css do listy klas
     * 
     * @param string $class
     * @return \ZendY\Form\CssTrait
     */
    public function addClass($class) {
        $classes = (array) $this->getAttrib('class');
        if (!in_array($class, $classes)) {
            $classes[] = $class;
        }
        $this->setAttrib('class', $classes);
        return $this;
    }

    /**
     * Dodaje wiele klas css na raz
     * 
     * @param array $classes
     * @return \ZendY\Form\CssTrait
     */
    public function addClasses(array $classes) {
        foreach ($classes as $class) {
            $this->addClass($class);
        }
        return $this;
    }
    
    /**
     * Ustawia klasę css (pojedynczą lub wiele) nadpisując istniejące
     * 
     * @param string|array $class
     * @return \ZendY\Form\CssTrait
     */
    public function setClass($class) {
        $this->setAttrib('class', (array) $class);
        return $this;
    }

    /**
     * Zwraca listę klas css
     * 
     * @return array
     */
    public function getClasses() {
        return $this->getAttrib('class');
    }

    /**
     * Usuwa klasę css z listy klas
     * 
     * @param string $class
     * @return \ZendY\Form\CssTrait
     */
    public function removeClass($class) {
        $classes = (array) $this->getAttrib('class');
        if (false !== $key = array_search($class, $classes)) {
            unset($classes[$key]);
        }
        $this->setAttrib('class', $classes);
        return $this;
    }

    /**
     * Usuwa wiele klas css z listy klas
     * 
     * @param array $classes
     * @return \ZendY\Form\CssTrait
     */
    public function removeClasses(array $classes) {
        foreach ($classes as $class) {
            $this->removeClass($class);
        }
        return $this;
    }

    /**
     * Ustawia wartość wybranej właściwości stylu css
     * 
     * @param string $property
     * @param mixed $value
     * @return \ZendY\Form\CssTrait
     */
    public function setStyle($property, $value) {
        $style = $this->getAttrib('style');
        $style[$property] = $value;
        $this->setAttrib('style', $style);
        return $this;
    }

    /**
     * Zwraca wartość wybranej właściwości stylu css
     * 
     * @param string $property
     * @return string|null
     */
    public function getStyle($property) {
        $style = (array) $this->getAttrib('style');
        if (array_key_exists($property, $style)) {
            return $style[$property];
        } else
            return NULL;
    }

    /**
     * Usuwa podaną właściwość stylu css
     * 
     * @param string $property
     * @return \ZendY\Form\CssTrait
     */
    public function removeStyle($property) {
        $style = $this->getAttrib('style');
        unset($style[$property]);
        $this->setAttrib('style', $style);
        return $this;
    }

    /**
     * Ustawia szerokość kontrolki przy użyciu stylu css. 
     * Jeśli wartość nie zostanie podana, szerokość zostanie zresetowana.
     * 
     * @param int $value
     * @param string $unit
     * @return \ZendY\Form\CssTrait
     */
    public function setWidth($value, $unit = 'px') {
        if (isset($value)) {
            $this->setStyle('width', array('value' => $value, 'unit' => $unit));
        } else {
            $this->removeStyle('width');
        }
        return $this;
    }

    /**
     * Zwraca szerokość kontrolki.
     * Wynikiem jest tablica dwuelementowa, gdzie 
     * pierwszym elementem jest wartość, drugim - jednostka.
     * 
     * @return array
     */
    public function getWidth() {
        return $this->getStyle('width');
    }

    /**
     * Ustawia wysokość kontrolki
     * Jeśli wartość nie zostanie podana, wysokość zostanie zresetowana.
     * 
     * @param string $value
     * @param string $unit
     * @return \ZendY\Form\CssTrait
     */
    public function setHeight($value, $unit = 'px') {
        if (isset($value)) {
            $this->setStyle('height', array('value' => $value, 'unit' => $unit));
        } else {
            $this->removeStyle('height');
        }
        return $this;
    }

    /**
     * Zwraca wysokość kontrolki
     * 
     * @return array
     */
    public function getHeight() {
        return $this->getStyle('height');
    }

    /**
     * Pozycjonuje formularz względem innych kontenerów poprzez klasę css
     * 
     * @param string $align
     * @return \ZendY\Form\CssTrait
     */
    public function setAlign($align) {
        $this->removeClasses(Css::$aligns)
                ->addClass($align);
        if ($align == Css::ALIGN_BOTTOM || $align == Css::ALIGN_TOP || $align == Css::ALIGN_CLIENT) {
            $this->setWidth(null);
        }
        if ($align == Css::ALIGN_LEFT || $align == Css::ALIGN_RIGHT || $align == Css::ALIGN_CLIENT) {
            $this->setHeight(null);
        }
        return $this;
    }

    /**
     * Zwraca klasę pozycjonowania
     * 
     * @return string|null
     */
    public function getAlign() {
        $result = false;
        foreach (Css::$aligns as $align) {
            $classes = $this->getClasses();
            if (isset($classes) && in_array($align, $classes)) {
                $result = $align;
            }
        }
        return $result;
    }

    /**
     * Dodaje margines absolutny do wybranego boku formularza
     * 
     * @param string $side
     * @param array $alignMargin
     * @return \ZendY\Form\CssTrait
     */
    public function addAlignMargin($side, array $alignMargin) {
        $this->setStyle($side, \ZendY\Form\Element\Widget::sumSizes(array(
                    $this->getStyle($side),
                    $alignMargin
                )));
        return $this;
    }

}
