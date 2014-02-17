<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Form\Element\ColumnInterface;
use ZendY\Exception;

/**
 * Bazodanowa kontrolka opcji radio
 *
 * @author Piotr Zając
 */
class Radio extends \ZendY\Form\Element\Radio implements ColumnInterface {

    use ColumnTrait;
    
    /**
     * Właściwości komponentu
     */

    const PROPERTY_DATASOURCE = 'dataSource';
    const PROPERTY_DATAFIELD = 'dataField';
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
     * @return array
     */
    public function getFrontNaviParams() {
        $this->setFrontNaviParam('keyField', $this->getKeyField());
        $this->setFrontNaviParam('listField', $this->getListField());
        $this->setFrontNaviParam('columnSpace', $this->getColumnSpace());
        $this->setFrontNaviParam('emptyValue', $this->getEmptyValue());
        $this->setFrontNaviParam('staticRender', $this->getStaticRender());
        $this->setFrontNaviParam('type', 'ra');
        return parent::getFrontNaviParams();
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('type', 'ra');
        $this->setFrontEditParam('dataField', $this->getDataField());
        return parent::getFrontEditParams();
    }

    /**
     * Zwraca pola ze zbioru danych potrzebne do wyrenderowania kontrolki
     *  
     * @return array
     */
    public function getFields() {
        return array_merge($this->getKeyField(), $this->getListField());
    }

    /**
     * Tworzy tablicę wartości dla statycznego renderowania listy
     * 
     * @return void
     * @throws Exception
     */
    protected function _performList() {
        if (isset($this->_listSource))
            $results = $this->_listSource->getDataSet()->getItems();
        else {
            $results = array();
        }

        $options = array();
        if ($this->_emptyValue) {
            $options[''] = '';
        }

        foreach ($results as $r) {
            //utworzenie wartości dla kontrolki
            $keyValueArray = array();
            if (is_object($r))
                $r = $r->toArray();

            foreach ($this->_keyField as $keyField) {
                if (!array_key_exists($keyField, $r)) {
                    throw new Exception(
                            'Kolumna klucza ' . $keyField . ' nie jest obecna w wyniku zapytania');
                }
                $keyValueArray[] = $r[$keyField];
            }

            $keyValueString = implode(';', $keyValueArray);

            $option = '';
            $c = 0;
            foreach ($this->_listField as $field) {
                if (!array_key_exists($field, $r)) {
                    throw new Exception(
                            'Kolumna wyświetlana ' . $field . ' nie jest obecna w wyniku zapytania');
                }
                if ($c > 0)
                    $option .= $this->_columnSpace;
                $option .= $r[$field];
                $c++;
            }
            $options[$keyValueString] = $option;
        }
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
