<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka do prezentacji danych na mapie
 *
 * @author Piotr Zając
 */
abstract class CustomMap extends Widget {
    /**
     * Parametry
     */

    const PARAM_ZOOM = 'zoom';
    const PARAM_CENTER = 'center';
    const PARAM_MAPTYPEID = 'mapTypeId';
    const PARAM_MARKERICON = 'icon';
    const PARAM_HIDDENCONTAINER = 'hiddenContainer';

    /**
     * Typy map
     */
    const MAPTYPEID_ROADMAP = 'google.maps.MapTypeId.ROADMAP';
    const MAPTYPEID_SATELLITE = 'google.maps.MapTypeId.SATELLITE';
    const MAPTYPEID_HYBRID = 'google.maps.MapTypeId.HYBRID';
    const MAPTYPEID_TERRAIN = 'google.maps.MapTypeId.TERRAIN';

    /**
     * Właściwości komponentu
     */
    const PROPERTY_ZOOM = 'zoom';
    const PROPERTY_CENTER = 'center';
    const PROPERTY_MAPTYPE = 'mapType';
    
    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
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
     * Ustawia wartości domyślne
     * 
     * @return void 
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        //ustawia domyślne parametry i atrybuty mapy
        $this->addClasses(array(
            Css::MAP,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
    }

    /**
     * Ustawia wskaźnik powiększenia mapy
     * 
     * @param int $zoom
     * @return \ZendY\Form\Element\CustomMap
     */
    public function setZoom($zoom) {
        $this->jQueryParams['map'][self::PARAM_ZOOM] = $zoom;
        return $this;
    }

    /**
     * Zwraca wskaźnik powiększenia mapy
     * 
     * @return int
     */
    public function getZoom() {
        return $this->jQueryParams['map'][self::PARAM_ZOOM];
    }

    /**
     * Wyśrodkowuje mapę względem podanego punktu
     * 
     * @param array $center
     * @return \ZendY\Form\Element\CustomMap
     */
    public function setCenter(array $center) {
        $this->jQueryParams['map'][self::PARAM_CENTER] = $center;
        return $this;
    }

    /**
     * Zwraca punkt środka mapy
     * 
     * @return array
     */
    public function getCenter() {
        return $this->jQueryParams['map'][self::PARAM_CENTER];
    }

    /**
     * Ustawia typ mapy
     * 
     * @param string $mapType
     * @return \ZendY\Form\Element\CustomMap
     */
    public function setMapType($mapType) {
        $this->jQueryParams['map'][self::PARAM_MAPTYPEID] = $mapType;
        return $this;
    }

    /**
     * Zwraca typ mapy
     * 
     * @return string
     */
    public function getMapType() {
        return $this->jQueryParams['map'][self::PARAM_MAPTYPEID];
    }

    /**
     * Ustawia ikonę markera
     * 
     * @param string $icon
     * @return \ZendY\Form\Element\CustomMap
     */
    public function setMarkerIcon($icon) {
        $this->jQueryParams['marker'][self::PARAM_MARKERICON] = $icon;
        return $this;
    }

    /**
     * Zwraca ikonę markera
     * 
     * @return string
     */
    public function getMarkerIcon() {
        return $this->jQueryParams['marker'][self::PARAM_MARKERICON];
    }

    /**
     * Ustawia mapę jako domyślnie ukrytą, 
     * jej inicjalizacja nastąpi po odkryciu wskazanego kontenera
     * 
     * @param string $hiddenContainer
     * @return \ZendY\Form\Element\CustomMap
     */
    public function setHiddenContainer($hiddenContainer) {
        $this->jQueryParams[self::PARAM_HIDDENCONTAINER] = $hiddenContainer;
        return $this;
    }

    /**
     * Zwraca identyfikator ukrytego kontenera mapy
     * 
     * @return string|bool
     */
    public function getHiddenContainer() {
        if (array_key_exists(self::PARAM_HIDDENCONTAINER, $this->jQueryParams))
            return $this->jQueryParams[self::PARAM_HIDDENCONTAINER];
        else
            return FALSE;
    }

    /**
     * Ustawia etykietę
     * 
     * @param string $label
     * @param array|string|null $width
     * @return \ZendY\Form\Element\CustomMap
     */
    public function setLabel($label, $width = null) {
        $this->setAttrib('label', $label);
        return $this;
    }

}
