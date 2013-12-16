<?php

namespace ZendY;

/**
 * Cechy klas kontrolek posiadających parametry jQuery przekazywane do przeglądarki
 *
 * @author Piotr Zając
 */
trait JQueryTrait {

    /**
     * Parametry jQuery kontrolki
     *
     * @var array
     */
    protected $jQueryParams = array();

    /**
     * Ustawia parametr jQuery
     *
     * @param  string $key
     * @param  string $value
     * @return \ZendY\Form\Container\Custom|\ZendY\Control
     */
    public function setJQueryParam($key, $value) {
        $key = (string) $key;
        if (isset($value)) {
            if (array_key_exists($key, $this->jQueryParams) && is_array($this->jQueryParams[$key])) {
                $this->jQueryParams[$key][] = $value;
            }
            else
                $this->jQueryParams[$key] = $value;
        } else {
            unset($this->jQueryParams[$key]);
        }
        return $this;
    }

    /**
     * Ustawia wiele parametrów jQuery (nadpisując istniejące)
     *
     * @param  array $params
     * @return \ZendY\Form\Container\Custom|\ZendY\Control
     */
    public function setJQueryParams($params) {
        $this->jQueryParams = $params;
        return $this;
    }

    /**
     * Dodaje tablicę parametrów jQuery
     *
     * @param  array $params
     * @return \ZendY\Form\Container\Custom|\ZendY\Control
     */
    public function addJQueryParams($params) {
        $this->jQueryParams = array_merge($this->jQueryParams, $params);
        return $this;
    }

    /**
     * Zwraca parametr jQuery o podanym kluczu
     *
     * @param  string $key
     * @return string
     */
    public function getJQueryParam($key) {
        $key = (string) $key;
        return $this->jQueryParams[$key];
    }

    /**
     * Zwraca wszystkie parametry jQuery
     *
     * @return array
     */
    public function getJQueryParams() {
        return $this->jQueryParams;
    }
    
    /**
     * Usuwa wybrany parametr jQuery
     * 
     * @param string $param
     * @return \ZendY\Form\Container\Custom|\ZendY\Control
     */
    public function removeJQueryParam($param) {
        if (array_key_exists($param, $this->jQueryParams))
            unset($this->jQueryParams[$param]);
        return $this;
    }
    

}

