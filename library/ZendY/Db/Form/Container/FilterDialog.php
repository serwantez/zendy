<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Container;

use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Form\Container\Dialog;
use ZendY\Css;
use ZendY\Db\DataInterface;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Form\Element;
use ZendY\Db\Form\Element as DbElement;

/**
 * Okno dialogowe do wykonywania akcji filtujących na zbiorze
 *
 * @author Piotr Zając
 */
class FilterDialog extends Dialog implements DataInterface {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_DATASOURCE = 'dataSource';

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Źródło danych
     * 
     * @var \ZendY\Db\DataSource 
     */
    protected $_dataSource;

    /**
     * Panel nawigacyjny w dolnej części kontenera
     * 
     * @var \ZendY\Db\Form\Container\Navigator
     */
    protected $_navigator;

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_AUTOOPEN,        
        self::PROPERTY_CLASSES,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_MODAL,
        self::PROPERTY_NAME,
        self::PROPERTY_OPENERS,
        self::PROPERTY_TITLE,
        self::PROPERTY_WIDTH
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->setTitle('Filter');
        $this->_navigator = new Navigator();
        $actions = array(
            array('action' => DataSet::ACTION_FILTER, 'text' => true)
        );
        $this->_navigator->setActions($actions);
        $this->setModal(true)
                ->setJQueryParam(self::PARAM_RESIZABLE, false)
        ;
    }

    /**
     * Ustawia źródło danych
     * 
     * @param \ZendY\Db\DataSource|null $dataSource
     * @return \ZendY\Db\Form\Container\FilterDialog
     */
    public function setDataSource(&$dataSource) {
        $this->_dataSource = $dataSource;
        return $this;
    }

    /**
     * Zwraca źródło danych
     * 
     * @return \ZendY\Db\DataSource
     */
    public function getDataSource() {
        return $this->_dataSource;
    }

    /**
     * Zwraca identyfikatory elementów filtrujących
     * 
     * @return string
     */
    protected function _getFilterWidgetsId() {
        $elements = $this->getAllElements();
        $result = '';
        foreach ($elements as $key => $element) {
            if ($element instanceof DbElement\Filter\IconEdit) {
                if (strlen($result) > 0)
                    $result .= ', ';
                $result .= '#' . $element->getName();
            }
        }
        return $result;
    }

    /**
     * Obsługa zdarzenia dołączenia nawigatora do formularza nadrzędnego
     * 
     * @return \ZendY\Db\Form\Container\FilterDialog
     */
    public function onContain() {
        $btnClearFilter = new DbElement\Button('btnClearFilter');
        $btnClearFilter
                ->setDataSource($this->_dataSource)
                ->setDataAction(DataSet::ACTION_CLEARFILTER)
                ->setVisibleText(TRUE);

        $btnCancel = new Element\IconButton($this->_navigator->getName() . '_closeDialog');
        $btnCancel->setLabel('Close')
                ->setIcons(Css::ICON_CLOSE)
                ->setVisibleText(TRUE)
        ;
        $this->_navigator
                ->setDataSource($this->_dataSource)
                ->addElement($btnClearFilter)
                ->addElement($btnCancel)
                ->setAlign(Css::ALIGN_BOTTOM)
        ;
        $this->addContainer($this->_navigator);

        $btnFilter = $this->_navigator->getElement($this->_navigator->getName() . '_' . DataSet::ACTION_FILTER);

        if (isset($btnFilter)) {
            $this->addCloser($btnFilter);
            $createEvent = sprintf('$("%s").keypress(function(event) {
                if(event.keyCode==$.ui.keyCode.ENTER){
                    $("#%s").click();
                    return false;
                }
            });'
                    , $this->_getFilterWidgetsId()
                    , $btnFilter->getName()
            );
            $this->setJQueryParam(
                    self::PARAM_EVENT_CREATE
                    , $createEvent
            );
        }
        if (isset($btnClearFilter)) {
            $this->addCloser($btnClearFilter);
        }
        if (isset($btnCancel)) {
            $this->addCloser($btnCancel);
        }

        $this->removeAttrib('method');
        $this->refreshDecorators();
        return $this;
    }

}
