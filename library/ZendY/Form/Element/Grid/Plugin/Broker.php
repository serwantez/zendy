<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element\Grid\Plugin;

/**
 * Klasa do zarządzania wszystkimi wtyczkami grida
 *
 * @author Piotr Zając
 */
class Broker extends Custom {

    /**
     * Tablica wtyczek
     *
     * @var array
     */
    protected $_plugins = array();

    /**
     * Ustawia obiekt widoku
     * 
     * @param \Zend_View $view
     * @return \ZendY\Form\Element\Grid\Plugin\Broker
     */
    public function setView(\Zend_View $view) {
        $this->_view = $view;

        $this->_view->gridPluginBroker = array();
        $this->_view->gridPluginBroker['html'] = array();
        $this->_view->gridPluginBroker['js'] = array();
        $this->_view->gridPluginBroker['onload'] = array();
        return $this;
    }

    /**
     * Rejestruje wtyczkę
     * 
     * @param \ZendY\Form\Element\Grid\Plugin\Custom $plugin
     * @return \ZendY\Form\Element\Grid\Plugin\Broker
     */
    public function registerPlugin(Custom $plugin) {
        $plugin->setGrid($this->_grid);
        $plugin->setGridData($this->_gridData);

        $this->_plugins[] = $plugin;

        return $this;
    }

    /**
     * Wyrejestrowuje wtyczkę (podaną lub wszystkie podanej klasy)
     * 
     * @param \ZendY\Form\Element\Grid\Plugin\Custom|string $plugin
     * @return \ZendY\Form\Element\Grid\Plugin\Broker
     */
    public function unregisterPlugin($plugin) {
        if ($plugin instanceof Custom) {
            foreach ($this->_plugins as $key => $_plugin) {
                if ($plugin === $_plugin) {
                    unset($this->_plugins[$key]);
                }
            }
        } elseif (is_string($plugin)) {
            foreach ($this->_plugins as $key => $_plugin) {
                $type = get_class($_plugin);
                if ($plugin == $type) {
                    unset($this->_plugins[$key]);
                }
            }
        }
        return $this;
    }

    /**
     * Zwraca informację o tym, czy jest zarejestrowana wtyczka podanej klasy
     *
     * @param  string $class
     * @return bool
     */
    public function hasPlugin($class) {
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                return true;
            }
        }
        return false;
    }

    /**
     * Wyszukuje i zwraca wtyczki o podanej klasie
     *
     * @param  string $class
     * @return false|\ZendY\Form\Element\Grid\Plugin\Custom|array 
     * Zwraca false jeśli nie znaleziono wtyczki, 
     * obiekt wtyczki jeśli znaleziono tylko jedną 
     * lub tablicę wtyczek, gdy znaleziono więcej niż jedną wtyczkę podanej klasy
     */
    public function getPlugin($class) {
        $found = array();
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                $found[] = $plugin;
            }
        }

        switch (count($found)) {
            case 0:
                return false;
            case 1:
                return $found[0];
            default:
                return $found;
        }
    }

    /**
     * Zwraca wszystkie wtyczki
     *
     * @return array
     */
    public function getPlugins() {
        return $this->_plugins;
    }

    /**
     * Wywoływane zanim grid wyśle odpowiedź
     *
     * @return void
     */
    public function preResponse() {
        foreach ($this->_plugins as $plugin) {
            $plugin->setGridData($this->_gridData);
            $plugin->setView($this->_view);
            $plugin->preResponse();
        }
    }

    /**
     * Wywoływane po tym jak grid wyśle odpowiedź
     *
     * @return void
     */
    public function postResponse() {
        foreach ($this->_plugins as $plugin) {
            $plugin->setGridData($this->_gridData);
            $plugin->setView($this->_view);
            $plugin->postResponse();
        }
    }

    /**
     * Wywoływane zanim grid wyśle kod do przeglądarki
     * 
     * @return void
     */
    public function preRender() {
        foreach ($this->_plugins as $plugin) {
            $plugin->setView($this->_view);
            $plugin->preRender();
        }
    }

    /**
     * Wywoływane po tym jak grid wyśle kod do przeglądarki
     *
     * @return void
     */
    public function postRender() {
        foreach ($this->_plugins as $plugin) {
            $plugin->setGridData($this->_gridData);
            $plugin->setView($this->_view);
            $plugin->postRender();
        }
    }

}