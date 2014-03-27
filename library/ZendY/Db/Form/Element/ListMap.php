<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Filter;
use ZendY\Exception;

/**
 * Bazowa kontrolka mapy wyświetlającej listę obiektów ze zbioru danych
 *
 * @author Piotr Zając
 */
abstract class ListMap extends \ZendY\Form\Element\ListMap implements ListInterface {

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
        self::PROPERTY_CENTER,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_MAPTYPE,
        self::PROPERTY_NAME,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH,
        self::PROPERTY_ZOOM
    );

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Parametry nawigacyjne przekazywane do przeglądarki
     * 
     * @var array
     */
    protected $_frontNaviParams = array();

    /**
     * Tablica warunkowego formatowania wierszy
     * 
     * @var array
     */
    protected $_conditionalRowFormat = array();

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->setFrontNaviParam('type', 'mp');
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final protected function setMultiOptions(array $options) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Dodaje parametr przekazywany do przeglądarki
     * 
     * @param string $paramName
     * @param string $paramValue
     * @return \ZendY\Db\Form\Element\ListMap
     */
    public function setFrontNaviParam($paramName, $paramValue) {
        $this->_frontNaviParams[$paramName] = $paramValue;
        return $this;
    }

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
        $this->setFrontNaviParam('staticRender', $this->getStaticRender());
        return $this->_frontNaviParams;
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
     * Dodaje warunek formatujący wyświetlane rekordy
     * 
     * @param Filter|array $condition
     * @param string $rowFormat
     * @return \ZendY\Db\Form\Element\ListMap
     */
    public function addConditionalRowFormat($condition, $rowFormat) {
        if ($condition instanceof Filter)
            $condition = $condition->getFilters();
        $this->_conditionalRowFormat[] = array($condition, $rowFormat);
        return $this;
    }

    /**
     * Zwraca warunki formatujące wyświetlane rekordy
     * 
     * @return array
     */
    public function getConditionalRowFormat() {
        return $this->_conditionalRowFormat;
    }

    /**
     * Tworzy tablicę wartości dla statycznego renderowania listy
     * 
     * @return void
     * @throws Exception
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
