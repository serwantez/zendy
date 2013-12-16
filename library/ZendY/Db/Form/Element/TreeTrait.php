<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataSource;
use ZendY\Db\DataSet\TreeSetInterface;
use ZendY\Exception;

/**
 * Cecha kontrolek wyświetlających pola drzewiastego zbioru danych
 *
 * @author Piotr Zając
 */
trait TreeTrait {
    
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
     * Pole ikony przechowujące nazwę klasy css
     * 
     * @var string
     */
    protected $_iconField;

    /**
     * Pole przechowujące wartość "z lewej"
     * 
     * @var string 
     */
    protected $_leftField;

    /**
     * Pole przechowujące wartość "z prawej"
     * 
     * @var string 
     */
    protected $_rightField;

    /**
     * Pole przechowujące wartość "głębokości zagnieżdżenia"
     * 
     * @var string 
     */
    protected $_depthField;

    /**
     * Ustawia źródło listy
     * 
     * @param \ZendY\Db\DataSource|null $listSource
     * @return \ZendY\Db\Form\Element\TreeInterface
     * @throws Exception
     */
    public function setListSource(&$listSource) {
        if ($listSource instanceof DataSource) {
            $listSource->addNaviControl($this);
            if ($listSource->getDataSet() instanceof TreeSetInterface) {
                $this->_listSource = $listSource;
                $this->_leftField = $this->_listSource->getDataSet()->getLeftField();
                $this->_rightField = $this->_listSource->getDataSet()->getRightField();
                $this->_depthField = $this->_listSource->getDataSet()->getDepthField();
            } else {
                throw new Exception('Instance of TreeInterface must implement TreeSetInterface');
            }
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
     * @return \ZendY\Db\Form\Element\TreeInterface
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
     * @return \ZendY\Db\Form\Element\TreeInterface
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
     * @return \ZendY\Db\Form\Element\TreeInterface
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
     * Ustawia pole ikony
     * 
     * @param string $iconField
     * @return \ZendY\Db\Form\Element\TreeInterface
     */
    public function setIconField($iconField) {
        $this->_iconField = $iconField;
        return $this;
    }

    /**
     * Zwraca nazwę pola ikony
     * 
     * @return string
     */
    public function getIconField() {
        return $this->_iconField;
    }

    /**
     * Zwraca nazwę pola przechowującego wartość "z lewej"
     * 
     * @return string
     */
    public function getLeftField() {
        return $this->_leftField;
    }

    /**
     * Zwraca nazwę pola przechowującego wartość "z prawej"
     * 
     * @return string
     */
    public function getRightField() {
        return $this->_rightField;
    }

    /**
     * Zwraca nazwę pola przechowującego wartość "głębokości zagnieżdżenia"
     * 
     * @return string 
     */
    public function getDepthField() {
        return $this->_depthField;
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
