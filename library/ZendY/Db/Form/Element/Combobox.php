<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Exception;

/**
 * Bazodanowa kontrolka listy rozwijanej
 *
 * @author Piotr Zając
 */
class Combobox extends \ZendY\Form\Element\Combobox implements ListInterface {

    use ListTrait;

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
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Tablica właściwości komponentu
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
        self::PROPERTY_EMPTYVALUE,
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
     * Zwraca tablicę parametrów nawigacyjnych przekazywanych do przeglądarki
     * 
     * @param string $list 
     * @return array
     */
    public function getFrontNaviParams($list = 'standard') {
        if ($list == 'standard') {
            $this->setFrontNaviParam('keyField', $this->getKeyField());
            $this->setFrontNaviParam('listField', $this->getListField());
        }
        $this->setFrontNaviParam('columnSpace', $this->getColumnSpace());
        $this->setFrontNaviParam('emptyValue', $this->getEmptyValue());
        $this->setFrontNaviParam('staticRender', $this->getStaticRender());
        return parent::getFrontNaviParams();
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('dataField', $this->getDataField());
        return parent::getFrontEditParams();
    }

    /**
     * Tworzy tablicę wartości dla statycznego renderowania listy
     * 
     * @return void
     * @throws Exception
     */
    protected function _performList() {
        if ($this->hasListSource()) {
            $results = $this->getListSource()->getDataSet()->getItems();
        } else {
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

            foreach ($this->getKeyField() as $keyField) {
                if (!array_key_exists($keyField, $r)) {
                    throw new Exception(
                            'Kolumna klucza ' . $keyField . ' nie jest obecna w wyniku zapytania');
                }
                $keyValueArray[] = $r[$keyField];
            }

            $keyValueString = implode(';', $keyValueArray);

            $option = '';
            $c = 0;
            foreach ($this->getListField() as $field) {
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
