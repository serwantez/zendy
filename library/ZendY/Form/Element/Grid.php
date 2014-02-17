<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Form\Element\Grid\Plugin;
use ZendY\Form\Element\Grid\Column;
use ZendY\Css;

/**
 * Siatka danych
 *
 * @author Piotr Zając
 */
class Grid extends CustomList {

    use \ZendY\ControlTrait;

    /**
     * Właściwości komponentu
     */

    const PROPERTY_COLUMNS = 'columns';
    const PROPERTY_PAGER = 'pager';
    const PROPERTY_SORTER = 'sorter';

    /**
     * Parametry
     */
    const PARAM_RECORDPERPAGE = 'recordPerPage';
    const PARAM_FIRSTCOLWIDTH = 'firstColWidth';

    /**
     * Zdarzenia
     */
    const PARAM_EVENT_DBLCLICKROW = 'dblClickRow';

    /**
     * Wartości domyślne
     */
    const DEFAULT_RECORDPERPAGE = 20;
    const DEFAULT_FIRSTCOLWIDTH = 19;

    /**
     * Tablica właściwości
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_COLUMNS,
        self::PROPERTY_COLUMNSPACE,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_DISABLED,
        self::PROPERTY_EMPTYVALUE,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_MULTIOPTIONS,
        self::PROPERTY_NAME,
        self::PROPERTY_PAGER,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_SORTER,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

    /**
     * Obiekt zarządzający wtyczkami do grida
     * 
     * @var \ZendY\Form\Element\Grid\Plugin\Broker
     */
    protected $_plugins;

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->_events = array(
            self::PARAM_EVENT_DBLCLICKROW,
        );
        $this->helper = 'grid';
        $this->_plugins = new Plugin\Broker();
        $this->_plugins->setGrid($this);
        $this->setRegisterInArrayValidator(false)
                ->setFirstColWidth(self::DEFAULT_FIRSTCOLWIDTH)
                ->setAttrib('columns', array())
                ->addClasses(array(
                    Css::GRID,
                    Css::WIDGET,
                    Css::WIDGET_CONTENT,
                    Css::CORNER_ALL,
                ))
                ->setWidth(600)
                ->setHeight(500)
        ;
    }

    /**
     * Dodaje kolumnę
     * 
     * @param \ZendY\Form\Element\Grid\Column $column
     * @return \ZendY\Form\Element\Grid
     */
    public function addColumn(Column $column) {
        $columns = $this->getColumns();
        $columns[$column->getName()] = $column;
        $this->setAttrib('columns', $columns);
        return $this;
    }

    /**
     * Dodaje wiele kolumn na raz
     * 
     * @param array $columns
     * @return \ZendY\Form\Element\Grid
     */
    public function addColumns(array $columns) {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
        return $this;
    }

    /**
     * Usuwa wszystkie kolumny
     * 
     * @return \ZendY\Form\Element\Grid
     */
    public function clearColumns() {
        $this->setAttrib('columns', null);
        return $this;
    }

    /**
     * Ustawia wszystkie kolumny na raz
     * 
     * @param array $columns
     * @return \ZendY\Form\Element\Grid
     */
    public function setColumns(array $columns) {
        $this->clearColumns();
        $this->addColumns($columns);
        return $this;
    }

    /**
     * Zwraca kolumnę o podanym identyfikatorze
     * 
     * @param string $columnId
     * @return \ZendY\Form\Element\Grid\Column
     */
    public function getColumn($columnId) {
        $columns = $this->getAttrib('columns');
        return $columns[$columnId];
    }

    /**
     * Zwraca wszystkie kolumny
     * 
     * @return array 
     */
    public function getColumns() {
        return $this->getAttrib('columns');
    }

    /**
     * Zwraca właściwości kolumn
     * 
     * @return array
     */
    public function getColumnsOptions() {
        $options = array();
        foreach ($this->getColumns() as $column) {
            $options[$column->getName()] = $column->getAttribs();
        }
        return $options;
    }

    /**
     * Ładuje domyślne dekoratory
     * 
     * @return void
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->setDecorators(array(
                array('UiWidgetMultiElement')
            ));
        }
    }

    /**
     * Ładuje dekoratory
     * 
     * @return void
     */
    public function loadDecorators() {
        $this->_labelOptions['id'] = $this->getName();
        $this->_labelOptions['class'] = Css::WIDGET . ' '
                . Css::WIDGET_HEADER . ' '
                . Css::CORNER_TOP . ' '
                . Css::PADDING_ALL . ' '
                . Css::GRID_LABEL;
        $this->setDecorators(array(
            array('UiWidgetMultiElement'),
            array('Label', $this->_labelOptions),
            array(array('Section' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field-container'))
        ));
    }

    /**
     * Rejestruje wtyczkę
     * 
     * @param \ZendY\Form\Element\Grid\Plugin\Custom $plugin
     * @return \ZendY\Form\Element\Grid
     */
    public function registerPlugin(Plugin\Custom $plugin) {
        $this->_plugins->registerPlugin($plugin);
        return $this;
    }

    /**
     * Zwraca informację o tym, czy podana wtyczka jest już zarejestrowana w gridzie
     * 
     * @param string $pluginClass
     * @return bool
     */
    public function hasPlugin($pluginClass) {
        return $this->_plugins->hasPlugin($pluginClass);
    }

    /**
     * Wyrejestrowanie wtyczki
     * 
     * @param \ZendY\Form\Element\Grid\Plugin\Custom|string $plugin
     * @return \ZendY\Form\Element\Grid
     */
    public function unregisterPlugin($plugin) {
        $this->_plugins->unregisterPlugin($plugin);
        return $this;
    }

    /**
     * Oblicza i zwraca wewnętrzną szerokość grida (na podstawie szerokości kolumn)
     * 
     * @return array
     */
    public function getInnerGridWidth() {
        $scrollWidth = 18;
        $borderWidth = 1;
        $padding = 2;
        $gridWidth = 0;
        foreach ($this->getColumns() as $key => $column) {
            $width = $column->getWidth();
            $gridWidth += $width['value'] + 2 * $padding + $borderWidth;
            $gridWidthUnit = $width['unit'];
        }
        return array('value' => $gridWidth, 'unit' => $gridWidthUnit);
    }

    /**
     * Ustawia liczbę rekordów na stronę
     * 
     * @param int $rpp
     * @return \ZendY\Form\Element\Grid
     */
    public function setRecordPerPage($rpp) {
        $this->setJQueryParam(self::PARAM_RECORDPERPAGE, $rpp);
        return $this;
    }

    /**
     * Zwraca liczbę rekordów na stronę
     * 
     * @return int
     */
    public function getRecordPerPage() {
        if (array_key_exists(self::PARAM_RECORDPERPAGE, $this->jQueryParams))
            return $this->getJQueryParam(self::PARAM_RECORDPERPAGE);
        else
            return self::DEFAULT_RECORDPERPAGE;
    }

    /**
     * Ustawia szerokość pierwszej kolumny (informującej o zaznaczonych wierszach)
     * 
     * @param int $width
     * @return \ZendY\Form\Element\Grid
     */
    public function setFirstColWidth($width) {
        $this->setJQueryParam(self::PARAM_FIRSTCOLWIDTH, $width);
        return $this;
    }

    /**
     * Zwraca szerokość pierwszej kolumny
     * 
     * @return int
     */
    public function getFirstColWidth() {
        if (array_key_exists(self::PARAM_FIRSTCOLWIDTH, $this->jQueryParams))
            return $this->getJQueryParam(self::PARAM_FIRSTCOLWIDTH);
        else
            return self::DEFAULT_FIRSTCOLWIDTH;
    }

    /**
     * Rejestruje wtyczkę stronicującą dane
     * 
     * @param int|null $recordPerPage
     * @return \ZendY\Form\Element\Grid
     */
    public function setPager($recordPerPage = self::DEFAULT_RECORDPERPAGE) {
        $this->registerPlugin(new Plugin\Pager())
                ->setRecordPerPage($recordPerPage);
        return $this;
    }

    /**
     * Zwraca liczbę rekordów na stronę, alias dla getRecordPerPage()
     * 
     * @return int
     */
    public function getPager() {
        return $this->getRecordPerPage();
    }

    /**
     * Rejestruje wtyczkę sortującą dane
     * 
     * @param bool $sorter
     * @return \ZendY\Form\Element\Grid
     */
    public function setSorter($sorter = true) {
        if ($sorter) {
            $this->registerPlugin(new Plugin\Sorter());
        } else {
            $this->unregisterPlugin('ZendY\Form\Element\Grid\Plugin\Sorter');
        }
        return $this;
    }

    /**
     * Zwraca informację o zarejestrowaniu wtyczki sortującej
     * 
     * @return bool
     */
    public function getSorter() {
        return $this->hasPlugin('ZendY\Form\Element\Grid\Plugin\Sorter');
    }

    /**
     * Tworzy kod html grida
     * 
     * @param \Zend_View_Interface $view
     * @return string 
     */
    public function render(\Zend_View_Interface $view = null) {
        if (null !== $view) {
            $this->setView($view);
        }

        $view = $this->getView();

        $this->_plugins->setView($view);
        $this->_plugins->preRender();

        $this->_plugins->postRender();

        $html = parent::render($view);

        return $html;
    }

}