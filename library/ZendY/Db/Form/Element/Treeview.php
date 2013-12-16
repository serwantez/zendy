<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Form\Element\TreeInterface;

/**
 * Kontrolka bazodanowa prezentująca dane w postaci struktury drzewiastej
 *
 * @author Piotr Zając
 */
class Treeview extends \ZendY\Form\Element\Treeview implements TreeInterface {

    use TreeTrait;

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Zwraca tablicę parametrów nawigacyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontNaviParams() {
        $this->setFrontNaviParam('type', 'tv');
        $this->setFrontNaviParam('keyField', $this->getKeyField());
        $this->setFrontNaviParam('listField', $this->getListField());
        $this->setFrontNaviParam('staticRender', $this->getStaticRender());
        $this->setFrontNaviParam('columnSpace', $this->getColumnSpace());
        $this->setFrontNaviParam('emptyValue', $this->getEmptyValue());
        $this->setFrontNaviParam('leftField', $this->getLeftField());
        $this->setFrontNaviParam('rightField', $this->getRightField());
        $this->setFrontNaviParam('iconField', $this->getIconField());
        $this->setFrontNaviParam('depthField', $this->getDepthField());
        $this->setFrontNaviParam('icons', $this->getIcons());
        return parent::getFrontNaviParams();
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('type', 'tv');
        $this->setFrontEditParam('dataField', $this->getDataField());
        return parent::getFrontEditParams();
    }

    /**
     * Zwraca pola ze zbioru danych potrzebne do wyrenderowania kontrolki
     * 
     * @return array
     */
    public function getFields() {
        $fields = array_merge($this->getKeyField(), $this->getListField()
                , array(
            $this->getLeftField(),
            $this->getRightField(),
            $this->getDepthField()
                ));
        if ($this->getIconField())
            $fields[] = $this->getIconField();
        return $fields;
    }

    /**
     * Tworzy tablicę wartości dla statycznego renderowania listy
     * 
     * @todo convert array to tree
     * @return void
     * @throws ZendY_Exception
     */
    protected function _performList() {
        if (isset($this->_listSource))
            $results = $this->_listSource->getItems();
        else {
            $results = array();
        }

        $options = array();
        if ($this->_emptyValue) {
            $options[''] = '';
        }
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
        //renderowanie statyczne
        if ($this->_staticRender) {
            $this->setDisabled(FALSE);
            $this->_performList();
        }
        return parent::render($view);
    }

}
