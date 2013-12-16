<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataSource;

/**
 * Cecha kontrolek wyświetlających pole zbioru danych
 *
 * @author Piotr Zając
 */
trait ColumnTrait {

    use CellTrait;

    /**
     * Pole klucza
     * 
     * @var array 
     */
    protected $_keyField = array('id');

    /**
     * Źródło wyświetlanej listy
     * 
     * @var \ZendY\Db\DataSource 
     */
    protected $_listSource;

    /**
     * Pole ze zbioru (źródła) danych wyświetlanej listy
     * 
     * @var array
     */
    protected $_listField = array();

    /**
     * Czy renderowanie listy opcji ma się odbyć przy renderowaniu całej kontrolki (statycznie)
     * 
     * @var bool 
     */
    protected $_staticRender = false;

    /**
     * Ustawia źródło listy
     * 
     * @param \ZendY\Db\DataSource|null $listSource 
     * @return \ZendY\Db\Form\Element\ColumnInterface
     */
    public function setListSource(&$listSource) {
        if ($listSource instanceof DataSource) {
            $listSource->addNaviControl($this);
            $this->_listSource = $listSource;
        }
        return $this;
    }

    /**
     * Zwraca źródło listy
     * 
     * @return \ZendY\Db\DataSource 
     */
    public function getListSource() {
        return $this->_listSource;
    }

    /**
     * Czy jest ustawione źródło listy
     * 
     * @return bool
     */
    public function hasListSource() {
        if (isset($this->_listSource))
            return true;
        else
            return false;
    }

    /**
     * Ustawia pole listy
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\ColumnInterface
     */
    public function setListField($name) {
        if (!is_array($name))
            $name = array($name);
        $this->_listField = $name;
        if (isset($this->_listSource))
            $this->_listSource->refreshNaviControl($this);
        return $this;
    }

    /**
     * Zwraca wyświetlane kolumny
     * 
     * @return array
     */
    public function getListField() {
        return $this->_listField;
    }

    /**
     * Ustawia pola klucza listy
     * 
     * @param string|array $name
     * @return \ZendY\Db\Form\Element\ColumnInterface
     */
    public function setKeyField($name) {
        if (!is_array($name))
            $name = array($name);
        $this->_keyField = $name;
        return $this;
    }

    /**
     * Zwraca pola klucza listy
     * 
     * @return array
     */
    public function getKeyField() {
        return $this->_keyField;
    }

    /**
     * Ustawia statyczne renderowanie listy opcji
     * 
     * @param bool $staticRender
     * @return \ZendY\Db\Form\Element\ColumnInterface
     */
    public function setStaticRender($staticRender = TRUE) {
        $this->_staticRender = $staticRender;
        return $this;
    }

    /**
     * Zwraca informację o tym czy renderowanie listy ma się odbywać statycznie
     * 
     * @return bool
     */
    public function getStaticRender() {
        return $this->_staticRender;
    }

    /**
     * Renderuje kod js odpowiedzialny za dostarczanie danych do kontrolki
     * 
     * @return string
     */
    public function renderDbNavi() {
        $js = sprintf(
                'ds.addNavi("%s",%s);'
                , $this->getId()
                , \ZendY\JQuery::encodeJson($this->getFrontNaviParams())
        );
        return $js;
    }

}
