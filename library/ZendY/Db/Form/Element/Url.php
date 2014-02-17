<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\Form\Element\CellInterface;

/**
 * Kontrolka adresu strony internetowej
 *
 * @author Piotr Zając
 */
class Url extends \ZendY\Form\Element\Url implements CellInterface {

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
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_LABEL,
        self::PROPERTY_ICON,
        self::PROPERTY_ICON_POSITION,
        self::PROPERTY_MAXLENGTH,
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
        $this->setFrontEditParam('type', 'le');
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