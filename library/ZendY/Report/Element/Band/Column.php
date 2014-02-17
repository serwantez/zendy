<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Element\Band;

use ZendY\Exception;
use ZendY\Component;

/**
 * Kolumna wstęgi raportu
 *
 * @author Piotr Zając
 */
class Column extends Component {

    const DEFAULT_WIDTH = 100;

    /**
     * Nazwa kolumny
     * 
     * @var string
     */
    protected $_name = null;

    /**
     * Konstruktor
     * 
     * @param array|Zend_Config|null @options
     * @return void
     */
    public function __construct($options = null) {
        $this->setSortable();
        parent::__construct($options);
    }

    /**
     * Retrieve plugin loader for validator or filter chain
     * 
     * @param string $type
     * @return @return \Zend_Loader_PluginLoader
     * @throws Exception
     */
    public function getPluginLoader($type) {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
                $prefixSegment = 'Report\\Element\\Band\\Column\\Decorator';
                $pathSegment = 'Report/Element/Band/Column/Decorator';
                if (!isset($this->_loaders[$type])) {
                    require_once 'Zend/Loader/PluginLoader.php';
                    $this->_loaders[$type] = new \Zend_Loader_PluginLoader(
                                    array('ZendY\\' . $prefixSegment . '\\' => 'ZendY/' . $pathSegment . '/')
                    );
                }
                return $this->_loaders[$type];
            default:
                throw new Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Ustawia etykietę kolumny
     * 
     * @param string $label
     * @return \ZendY\Report\Element\Band\Column
     */
    public function setLabel($label) {
        $this->setAttrib('label', $label);
        return $this;
    }

    /**
     * Zwraca etykietę kolumny
     * 
     * @return string
     */
    public function getLabel() {
        $label = $this->getAttrib('label');
        if (!isset($label))
            $label = $this->getName();

        if (null !== ($translator = $this->getTranslator())) {
            return $translator->translate($label);
        }
        return $label;
    }

    /**
     * Ustawia szerokość kolumny
     * 
     * @param int $value
     * @param string $unit
     * @return ZendY\Report\Element\Band\Column
     */
    public function setWidth($value, $unit = 'px') {
        $this->setAttrib('width', array('value' => $value, 'unit' => $unit));
        return $this;
    }

    /**
     * Zwraca szerokość kolumny
     * 
     * @return array
     */
    public function getWidth() {
        $width = $this->getAttrib('width');
        if (!isset($width)) {
            $width = array('value' => self::DEFAULT_WIDTH, 'unit' => 'px');
        }
        return $width;
    }

    /**
     * Ustawia wyrównanie komórek
     * 
     * @param string $align
     * @return ZendY\Report\Element\Band\Column
     */
    public function setAlign($align) {
        $this->setAttrib('align', $align);
        return $this;
    }

    /**
     * Zwraca wyrównanie komórek
     * 
     * @return string
     */
    public function getAlign() {
        return $this->getAttrib('align');
    }

    /**
     * Ustawia sortowanie po kolumnie
     * 
     * @param bool $sort
     * @return ZendY\Report\Element\Band\Column
     */
    public function setSortable($sortable = TRUE) {
        $this->setAttrib('sortable', $sortable);
        return $this;
    }

    /**
     * Zwraca sortowanie po kolumnie
     * 
     * @return bool
     */
    public function getSortable() {
        return $this->getAttrib('sortable');
    }

    /**
     * Wartość kolumny w podanym wierszu
     * 
     * @param array $row
     * @return string 
     */
    public function cellValue(array $row) {
        $row[$this->getName()] = htmlspecialchars($row[$this->getName()]);
        $decorators = $this->getDecorators();
        foreach ($decorators as $decorator) {
            if ($decorator instanceof Column\Decorator\Custom) {
                $row[$this->getName()] = $decorator->cellValue($row);
            }
        }
        return $row[$this->getName()];
    }

    /**
     * Ustawia nazwę kolumny
     * 
     * @param string $name
     * @return ZendY\Report\Element\Band\Column
     */
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    /**
     * Zwraca nazwę kolumny
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

}

