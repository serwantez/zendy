<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Form\Element\CellInterface;

/**
 * Kontrolka do wprowadzania i odczytywania wielolinijkowych wartości tekstowych
 *
 * @author Piotr Zając
 */
class Textarea extends \ZendY\Form\Element\Textarea implements CellInterface {

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
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_MAXLENGTH,
        self::PROPERTY_NAME,
        self::PROPERTY_PLACEHOLDER,
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
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('type', 'ta');
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
