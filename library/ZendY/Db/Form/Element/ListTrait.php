<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataSource;
use ZendY\Exception;

/**
 * Cecha kontrolek zawierających listy danych. 
 * Lista jest pomocniczym zbiorem danych, 
 * spośród których użytkownik wybiera jedną wartość.
 * Wybrana wartość może być zapisana w głównym zbiorze danych kontrolki
 * lub może służyć do filtrowania innych zbiorów.
 *
 * @author Piotr Zając
 */
trait ListTrait {

    use CellTrait;

    /**
     * Dane list o strukturze:
     * array(
     * 'listName' => array(
     *  'listSource' => $listSource, 
     *  'listField' => 'fieldsToShow', 
     *  'keyField' => 'fieldsToIndex'
     *  ),
     * )
     * 
     * @var array
     */
    protected $_lists = array();

    /**
     * Pole klucza
     * 
     * @var array 
     */
    //protected $_keyField = array('id');

    /**
     * Źródła wyświetlanych list
     * 
     * @var array
     */
    //protected $_listSource = array();

    /**
     * Pole ze zbioru (źródła) danych wyświetlanej listy
     * 
     * @var array
     */
    //protected $_listField = array();

    /**
     * Czy renderowanie listy opcji ma się odbyć przy renderowaniu całej kontrolki (statycznie)
     * 
     * @var bool 
     */
    protected $_staticRender = false;

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setMultiOptions(array $options) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Ustawia źródło listy
     * 
     * @param \ZendY\Db\DataSource|null $listSource 
     * @return \ZendY\Db\Form\Element\ListTrait
     */
    public function setListSource(&$listSource) {
        if ($listSource instanceof DataSource) {
            $listSource->addNaviControl($this);
            $this->_lists['standard']['listSource'] = $listSource;
        }
        return $this;
    }

    /**
     * Zwraca źródło listy
     * 
     * @return \ZendY\Db\DataSource 
     */
    public function getListSource() {
        if (isset($this->_lists['standard']['listSource']))
            return $this->_lists['standard']['listSource'];
        else
            return null;
    }

    /**
     * Czy jest ustawione źródło listy
     * 
     * @return bool
     */
    public function hasListSource() {
        if (isset($this->_lists['standard']['listSource']))
            return true;
        else
            return false;
    }

    /**
     * Ustawia pole listy
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\ListTrait
     */
    public function setListField($name) {
        if (!is_array($name))
            $name = array($name);
        $this->_lists['standard']['listField'] = $name;
        if ($this->hasListSource())
            $this->getListSource()->refreshNaviControl($this);
        return $this;
    }

    /**
     * Zwraca wyświetlane kolumny
     * 
     * @return array
     */
    public function getListField() {
        if (isset($this->_lists['standard']['listField']))
            return $this->_lists['standard']['listField'];
        else
            return null;
    }

    /**
     * Dodaje pojedyncze pole listy
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\ListTrait
     */
    public function addListField($name) {
        $this->_lists['standard']['listField'][] = $name;
        return $this;
    }

    /**
     * Ustawia pola klucza listy
     * 
     * @param string|array $name
     * @return \ZendY\Db\Form\Element\ListTrait
     */
    public function setKeyField($name) {
        if (!is_array($name))
            $name = array($name);
        $this->_lists['standard']['keyField'] = $name;
        return $this;
    }

    /**
     * Zwraca pola klucza listy
     * 
     * @return array
     */
    public function getKeyField() {
        if (isset($this->_lists['standard']['keyField']))
            return $this->_lists['standard']['keyField'];
        else
            return null;
    }

    /**
     * Ustawia wszystkie parametry używanych w kontrolce list
     * 
     * @param array $lists
     * @return \ZendY\Db\Form\Element\ListTrait
     */
    public function setLists(array $lists) {
        $this->_lists = $lists;
        return $this;
    }

    /**
     * Zwraca wszystkie parametry list
     * 
     * @return array
     */
    public function getLists() {
        return $this->_lists;
    }

    /**
     * Dodaje pojedynczą listę
     * 
     * @param array $list
     * @param string $type
     * @return \ZendY\Db\Form\Element\ListTrait
     */
    public function addList(array $list, $type = 'standard') {
        $this->_lists[$type] = $list;
        return $this;
    }

    /**
     * Usuwa wybraną listę
     * 
     * @param string $type
     * @return \ZendY\Db\Form\Element\ListTrait
     */
    public function removeList($type) {
        if (array_key_exists($type, $this->_lists))
            unset($this->_lists[$type]);
        return $this;
    }

    /**
     * Zwraca pola ze zbioru danych potrzebne do wyrenderowania kontrolki
     *  
     * @param string $source
     * @return array
     */
    public function getFields($list = 'standard') {
        if ($list == 'standard') {
            return array_unique(array_merge($this->getKeyField(), $this->getListField()));
        } else
            return array();
    }

    /**
     * Ustawia statyczne renderowanie listy opcji
     * 
     * @param bool $staticRender
     * @return \ZendY\Db\Form\Element\ListTrait
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
     * @param string $list
     * @return string
     */
    public function renderDbNavi($list = 'standard') {
        $js = sprintf(
                'ds.addNavi("%s",%s);'
                , $this->getId()
                , \ZendY\JQuery::encodeJson($this->getFrontNaviParams($list))
        );
        return $js;
    }

}
