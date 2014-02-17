<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Form\Element\CellInterface;

/**
 * Kontrolka prezentuje i przekazuje do zbioru danych współrzędne linii na mapie
 *
 * @author Piotr Zając
 */
class LineMap extends \ZendY\Form\Element\CustomMap implements CellInterface {

    use CellTrait;
    
    /**
     * Właściwości komponentu
     */

    const PROPERTY_DATASOURCE = 'dataSource';
    const PROPERTY_DATAFIELD = 'dataField';        
    
    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_DATAFIELD,
        self::PROPERTY_DATASOURCE,
        
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
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'lineMap';
        $this->setZoom(6);
        $this->setCenter(array(52, 20));
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('type', 'mp');
        $this->setFrontEditParam('dataField', $this->getDataField());
        return parent::getFrontEditParams();
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
        return parent::render($view);
    }

}
