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
     * Właściwości komponentu
     */

    const PROPERTY_DATASOURCE = 'dataSource';
    const PROPERTY_DATAFIELD = 'dataField';
    const PROPERTY_ICONFIELD = 'iconField';
    const PROPERTY_LISTSOURCE = 'listSource';
    const PROPERTY_LISTFIELD = 'listField';
    const PROPERTY_KEYFIELD = 'keyField';
    const PROPERTY_STATICRENDER = 'staticRender';

    /**
     * Tablica właściwości
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_DATAFIELD,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_ICONFIELD,
        self::PROPERTY_KEYFIELD,
        self::PROPERTY_LISTFIELD,
        self::PROPERTY_LISTSOURCE,
        self::PROPERTY_STATICRENDER,
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_COLUMNSPACE,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_MULTIOPTIONS,
        self::PROPERTY_NAME,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Zwraca tablicę parametrów nawigacyjnych przekazywanych do przeglądarki
     * 
     * @param string $list
     * @return array
     */
    public function getFrontNaviParams($list = 'standard') {
        if ($list == 'standard') {
            $this->setFrontNaviParam('keyField', $this->getKeyField());
            $this->setFrontNaviParam('listField', $this->getListField());
            $this->setFrontNaviParam('leftField', $this->getLeftField());
            $this->setFrontNaviParam('rightField', $this->getRightField());
            $this->setFrontNaviParam('iconField', $this->getIconField());
            $this->setFrontNaviParam('depthField', $this->getDepthField());
        }
        $this->setFrontNaviParam('type', 'tv');
        $this->setFrontNaviParam('staticRender', $this->getStaticRender());
        $this->setFrontNaviParam('columnSpace', $this->getColumnSpace());
        $this->setFrontNaviParam('emptyValue', $this->getEmptyValue());
        $this->setFrontNaviParam('icons', $this->getIcons());
        return parent::getFrontNaviParams($list);
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
     * @param string $source
     * @return array
     */
    public function getFields($list = 'standard') {
        if ($list == 'standard') {
            $fields = array_merge($this->getKeyField(), $this->getListField()
                    , array(
                $this->getLeftField(),
                $this->getRightField(),
                $this->getDepthField()
                    ));
            if ($this->getIconField())
                $fields[] = $this->getIconField();
            return $fields;
        } else
            return array();
    }

    /**
     * Tworzy tablicę wartości dla statycznego renderowania listy
     * 
     * @todo convert array to tree
     * @return void
     * @throws ZendY_Exception
     */
    protected function _performList() {
        if ($this->hasListSource())
            $results = $this->getListSource()->getItems();
        else {
            $results = array();
        }

        $options = array();
        if ($this->_emptyValue) {
            $options[''] = '';
        }

        /**
         * @todo static tree rendering
         */
        $this->clearMultiOptions();
        $this->addMultiOptions($options);
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
