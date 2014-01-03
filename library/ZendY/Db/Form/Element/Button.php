<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Form\Element\IconButton;
use ZendY\Db\Form\Element\ActionInterface;
use ZendY\Db\ActionManager;

/**
 * Klasa bazowa dla przycisku bazodanowego
 *
 * @author Piotr Zając
 */
class Button extends IconButton implements ActionInterface {

    use \ZendY\Db\DataTrait;

    /**
     * Licznik instancji
     * 
     * @var int 
     */
    static protected $count = 0;

    /**
     * Akcja przycisku
     * 
     * @var string
     */
    protected $_dataAction;

    /**
     * Parametry przekazywane do przeglądarki
     * 
     * @var array
     */
    protected $_frontActionParams = array();

    /**
     * Ustawia akcję przypisaną przycisku
     * 
     * @param string $dataAction
     * @return \ZendY\Db\Form\Element\Button
     */
    public function setDataAction($dataAction) {
        if (isset($this->_dataSource)) {
            $params = $this->_dataSource->getDataSet()->getActionParams($dataAction);
            $this->setIcons($params['icon']);
            $this->setLabel($params['text']);
            //$this->setShortKey($params['shortKey']);
            $this->_dataAction = $dataAction;
            $this->setFrontActionParam('type', 'bt');
            $this->setFrontActionParam('dataAction', $dataAction);
            $this->setFrontActionParam('actionType', $params['type']);
        }
        return $this;
    }

    /**
     * Zwraca akcję przypisaną do przycisku
     * 
     * @return string 
     */
    public function getDataAction() {
        return $this->_dataAction;
    }

    /**
     * Dodaje parametr przekazywany do przeglądarki
     * 
     * @param string $paramName
     * @param string|\Zend_Json_Expr $paramValue
     * @return \ZendY\Db\Form\Element\Button
     */
    public function setFrontActionParam($paramName, $paramValue) {
        $this->_frontActionParams[$paramName] = $paramValue;
        return $this;
    }

    /**
     * Ustawia parametry przekazywane do przeglądarki
     * 
     * @param array $params
     * @return \ZendY\Db\Form\Element\Button
     */
    public function setFrontActionParams(array $params) {
        $this->_frontActionParams = $params;
        return $this;
    }

    /**
     * Dodaje parametry przekazywane do przeglądarki
     * 
     * @param array $params
     * @return \ZendY\Db\Form\Element\Button
     */
    public function addFrontActionParams(array $params) {
        foreach ($params as $paramName => $paramValue) {
            $this->setFrontActionParam($paramName, $paramValue);
        }
        return $this;
    }

    /**
     * Zwraca wartość podanego parametru przekazywanego do przeglądarki
     * 
     * @return array
     */
    public function getFrontActionParam($paramName) {
        return $this->_frontActionParams[$paramName];
    }
    
    /**
     * Zwraca tablicę parametrów przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontActionParams() {
        return $this->_frontActionParams;
    }

    /**
     * Renderuje element sprawdzając uprawnienia
     * 
     * @param \Zend_View_Interface $view
     * @return null|string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasDataSource())
            $this->getDataSource()->addNaviControl($this);
        $resource = $this->getDataSource()->getDataSet()->getId();
        $privilege = $this->getDataSource()->getDataSet()->getActionPrivilege($this->_dataAction);
        $result = parent::render($view);
        //nie wyświetli przycisku, gdy użytkownik nie ma uprawnienia do jego akcji
        if (!ActionManager::allowed($resource, $privilege)) {
            $result = null;
        }
        return $result;
    }

    /**
     * Renderuje kod js odpowiedzialny za dostarczanie danych do kontrolki
     * 
     * @return string
     */
    public function renderDbNavi() {
        $js = sprintf(
                'ds.addAction("%s",%s);'
                , $this->getId()
                , \ZendY\JQuery::encodeJson($this->getFrontActionParams())
        );
        return $js;
    }

}
