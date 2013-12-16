<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element\Grid\Plugin;

use ZendY\Form\Element\Grid;

/**
 * Klasa bazowa dla wtyczek do grida
 *
 * @author Piotr Zając
 */
abstract class Custom {

    /**
     * Obiekt grida
     * 
     * @var \ZendY\Form\Element\Grid
     */
    protected $_grid;

    /**
     * Dane grida
     * 
     * @var array
     */
    protected $_gridData;

    /**
     * Obiekt widoku
     * 
     * @var \Zend_View
     */
    protected $_view;

    /**
     * Ustawia widok
     * 
     * @param \Zend_View $view
     * @return \ZendY\Form\Element\Grid\Plugin\Custom
     */
    public function setView(\Zend_View $view) {
        $this->_view = $view;
        return $this;
    }

    /**
     * Ustawia obiekt grida
     * 
     * @param \ZendY\Form\Element\Grid $grid
     * @return \ZendY\Form\Element\Grid\Plugin\Custom
     */
    public function setGrid(Grid $grid) {
        $this->_grid = $grid;
        return $this;
    }

    /**
     * Zwraca obiekt grida
     * 
     * @return \ZendY\Form\Element\Grid
     */
    public function getGrid() {
        return $this->_grid;
    }

    /**
     * Ustawia obiekt danych grida
     * 
     * @param array $data
     * @return \ZendY\Form\Element\Grid\Plugin\Custom
     */
    public function setGridData($data) {
        $this->_gridData = $data;
        return $this;
    }

    /**
     * Zwraca obiekt danych grida
     * 
     * @return array
     */
    public function getGridData() {
        return $this->_gridData;
    }

    /**
     * Dodaje do widoku wtyczki kod html
     * 
     * @param string $html
     * @return \ZendY\Form\Element\Grid\Plugin\Custom
     */
    public function addHtml($html) {
        $this->_view->gridPluginBroker['html'][] = $html;
        return $this;
    }

    /**
     * Dodaje do widoku wtyczki kod js ładowany przy uruchamianiu strony
     * 
     * @param string $js
     * @return \ZendY\Form\Element\Grid\Plugin\Custom
     */
    public function addOnLoad($js) {
        $this->_view->gridPluginBroker['onload'][] = $js;
        return $this;
    }

    /**
     * Dodaje do widoku wtyczki kod javascript
     * 
     * @param string $js
     * @param bool $onload
     * @return \ZendY\Form\Element\Grid\Plugin\Custom
     */
    public function addJavascript($js, $onload = false) {
        if ($onload == true) {
            return $this->addOnLoad($js);
        }

        $this->_view->gridPluginBroker['js'][] = $js;
        return $this;
    }

    /**
     * Wywoływane zanim grid wyśle odpowiedź
     * 
     * @return void
     */
    abstract public function preResponse();

    /**
     * Wywoływane po tym jak grid wyśle odpowiedź
     * 
     * @return void
     */
    abstract public function postResponse();

    /**
     * Wywoływane zanim grid wyśle kod do przeglądarki
     * 
     * @return void
     */
    abstract public function preRender();

    /**
     * Wywoływane po tym jak grid wyśle kod do przeglądarki
     * 
     * @return void
     */
    abstract public function postRender();
}