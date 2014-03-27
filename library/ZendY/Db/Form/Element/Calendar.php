<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

/**
 * Bazodanowa kontrolka kalendarza
 *
 * @author Piotr Zając
 */
class Calendar extends \ZendY\Form\Element\Calendar implements ListInterface, CalendarInterface {

    use ListTrait;

    /**
     * Właściwości komponentu
     */

    const PROPERTY_DATAFIELD = 'dataField';
    const PROPERTY_DATASOURCE = 'dataSource';
    const PROPERTY_DATEFIELD = 'dateField';
    const PROPERTY_HOLIDAYFIELD = 'holidayField';
    const PROPERTY_KEYFIELD = 'keyField';
    const PROPERTY_LISTFIELD = 'listField';
    const PROPERTY_LISTSOURCE = 'listSource';
    const PROPERTY_STATICRENDER = 'staticRender';

    /**
     * Tablica właściwości
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_CURRENTDATE,
        self::PROPERTY_DATAFIELD,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_DATEFIELD,
        self::PROPERTY_DIALOG,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HOLIDAYFIELD,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_KEYFIELD,
        self::PROPERTY_LABEL,
        self::PROPERTY_LISTFIELD,
        self::PROPERTY_LISTSOURCE,
        self::PROPERTY_NAME,
        self::PROPERTY_RANGE,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_STATICRENDER,
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
     * Pole daty
     * 
     * @var string
     */
    protected $_dateField;

    /**
     * Pole dnia wolnego od pracy
     * 
     * @var string
     */
    protected $_holidayField;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->setFrontNaviParam('type', 'ca');
        $this->setFrontEditParam('type', 'ca');
    }

    /**
     * Zwraca tablicę parametrów nawigacyjnych przekazywanych do przeglądarki
     * 
     * @param string $list
     * @return array
     */
    public function getFrontNaviParams($list = 'standard') {
        if ($list == 'standard') {
            $this->setFrontNaviParam('dateField', $this->getDateField());
            $this->setFrontNaviParam('holidayField', $this->getHolidayField());
            $this->setFrontNaviParam('keyField', $this->getKeyField());
            $this->setFrontNaviParam('listField', $this->getListField());
        }
        $this->setFrontNaviParam('dialog', $this->getDialog());
        $this->setFrontNaviParam('staticRender', $this->getStaticRender());
        return parent::getFrontNaviParams($list);
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
     * Zwraca pola ze zbioru danych listy potrzebne do wyrenderowania kontrolki
     *  
     * @param string $source
     * @return array
     */
    public function getFields($list = 'standard') {
        if ($list == 'standard') {
            return array_unique(array_merge(
                                    $this->getKeyField()
                                    , array(
                                $this->getDateField(),
                                $this->getHolidayField()
                                    ), $this->getListField()
                            ));
        } else
            return array();
    }

    /**
     * Ustawia pole daty
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\Calendar
     */
    public function setDateField($name) {
        $this->_dateField = $name;
        return $this;
    }

    /**
     * Zwraca pole daty
     * 
     * @return string
     */
    public function getDateField() {
        return $this->_dateField;
    }

    /**
     * Ustawia pole dnia wolnego od pracy
     * 
     * @param string $name
     * @return \ZendY\Db\Form\Element\Calendar
     */
    public function setHolidayField($name) {
        $this->_holidayField = $name;
        return $this;
    }

    /**
     * Zwraca pole dnia wolnego od pracy
     * 
     * @return string
     */
    public function getHolidayField() {
        return $this->_holidayField;
    }

    /**
     * Renderowanie kontrolki
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasDataSource())
            $this->getDataSource()->addEditControl($this);
        if ($this->hasListSource()) {
            $this->getListSource()->getDataSet()->setPeriod($this->getPeriod());
        }
        $this->setAttrib('lists', $this->getLists());
        return parent::render($view);
    }

}
