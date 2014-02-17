<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

/**
 * Mapa wyświetlająca listę linii
 *
 * @author Piotr Zając
 */
class LineListMap extends ListMap {
    
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
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'lineListMap';
        $this->setZoom(7);
        $this->setCenter(array(52, 20));
    }

    /**
     * Formatuje kolumny danych według zdefiniowanych dekoratorów
     * 
     * @param array $data
     * @return array
     */
    public function formatData(array $data = array()) {
        //todo
        return $data;
    }

}

