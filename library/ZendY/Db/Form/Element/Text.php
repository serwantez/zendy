<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Form\Element\CellInterface;
use ZendY\Db\Form\Element\PresentationInterface;
use ZendY\Css;

/**
 * Kontrolka do odczytywania krótkich wartości tekstowych
 *
 * @author Piotr Zając
 */
class Text extends \ZendY\Form\Element\Text implements CellInterface, PresentationInterface {

    use CellTrait;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'text';
        $this->addClasses(array(
            Css::TEXT,
            Css::WIDGET,
            Css::CORNER_ALL
        ));
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('type', 'tx');
        $this->setFrontEditParam('dataField', $this->getDataField());
        return parent::getFrontEditParams();
    }

    /**
     * Renderuje kontrolkę
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasDataSource())
            $this->getDataSource()->addEditControl($this);
        return parent::render($view);
    }

}
