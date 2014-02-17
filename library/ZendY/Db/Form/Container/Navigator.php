<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Container;

use ZendY\Form\Container\Base as Container;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Css;
use ZendY\Exception;
use ZendY\Db\DataSource;
use ZendY\Db\DataInterface;
use ZendY\Db\Form\Element as DbElement;

/**
 * Panel nawigatora do umieszczania przycisków akcji
 *
 * @author Piotr Zając
 */
class Navigator extends Container implements DataInterface {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_ACTIONS = 'actions';
    const PROPERTY_DATASOURCE = 'dataSource';
    const PROPERTY_EXPR = 'expr';

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
     * Akcje, które będzie mógł wykonać nawigator na zbiorze danych
     * 
     * @var array 
     */
    protected $_actions = array();

    /**
     * Typy kontrolek wyrażeń, które mają być wyświetlone w nawigatorze
     * 
     * @var array
     */
    protected $_expr = array(DataSet::EXPR_OFFSET, DataSet::EXPR_COUNT);

    /**
     * Dodatkowe kontrolki
     * 
     * @var array
     */
    protected $_userElements = array();

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ACTIONS,
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_EXPR,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_SPACE,
        self::PROPERTY_WIDGETCLASS,
        self::PROPERTY_WIDTH
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this
                ->setHeight(array('value' => 3.2, 'unit' => 'em'))
                ->setWidgetClass(Css::WIDGET_HEADER)
                ->addClass(Css::SCROLL_DISABLE)
                ->setAlign(Css::ALIGN_BOTTOM)
        ;
    }

    /**
     * Ustawia źródło danych
     * 
     * @param \ZendY\Db\DataSource|null $dataSource
     * @return \ZendY\Db\Form\Container\Navigator
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
     * Ustawia akcje nawigatora
     * 
     * @param array $actions
     * @return \ZendY\Db\Form\Container\Navigator
     */
    public function setActions(array $actions) {
        $this->_actions = $actions;
        return $this;
    }

    /**
     * Zwraca akcje nawigatora
     * 
     * @return array
     */
    public function getActions() {
        return $this->_actions;
    }

    /**
     * Ustawia typy kontrolek wyrażeń, które mają być wyświetlone w nawigatorze
     * 
     * @param array $expr
     * @return \ZendY\Db\Form\Container\Navigator
     */
    public function setExpr(array $expr) {
        $this->_expr = $expr;
        return $this;
    }

    /**
     * Zwraca typy kontrolek wyrażeń
     * 
     * @return array
     */
    public function getExpr() {
        return $this->_expr;
    }

    /**
     * Dodaje kontrolki bazodanowe
     * 
     * @return \ZendY\Db\Form\Container\Navigator
     */
    protected function _addDataControls() {
        if (isset($this->_dataSource) and $this->_dataSource instanceof DataSource) {
            if (!isset($this->_actions)) {
                $this->setActions($this->_dataSource->getDataSet()->getActions());
            }


            $i = 0;
            foreach ($this->_actions as $key => $value) {
                if (!is_array($value)) {
                    $nvalue['action'] = $value;
                    $nvalue['text'] = false;
                    $value = $nvalue;
                } else {
                    if (!array_key_exists('text', $value))
                        $value['text'] = false;
                }
                if ($this->_dataSource->getDataSet()->isRegisteredAction($value['action'])) {
                    $name = $this->getName() . '_' . $value['action'];
                    $element = new DbElement\Button($name);
                    $element
                            ->setDataSource($this->_dataSource)
                            ->setDataAction($value['action'])
                            ->setVisibleText($value['text'])
                    ;
                    //skrót klawiaturowy
                    if (isset($value['shortkey'])) {
                        $element->setShortKey($value['shortkey']);
                    }
                    //dodatkowe parametry przekazywane do przeglądarki
                    if (isset($value['params'])) {
                        $element->addFrontActionParams($value['params']);
                    }

                    parent::addElement($element);
                    //kontrolki informacyjne
                    if ($value['action'] == DataSet::ACTION_PREVIOUS) {

                        foreach ($this->getExpr() as $expr) {
                            $name = $this->getName() . '_' . $expr;
                            $element = new DbElement\Expr($name);
                            $element
                                    ->setDataSource($this->_dataSource)
                                    ->setExpr($expr)
                            //->setStyle('text-align', 'center')
                            ;
                            parent::addElement($element);
                            //$elements[] = $element;
                        }
                    }
                }
                $i++;
            }
        }
        return $this;
    }

    /**
     * Dodaje kontrolkę użytkownika
     * 
     * @param \ZendY\Form\Element\Widget $element
     * @param string|null $name
     * @param array|null|Zend_Config $options
     * @return \ZendY\Db\Form\Container\Navigator
     */
    public function addElement($element, $name = null, $options = null) {
        if ($element instanceof \ZendY\Form\Element\Widget) {
            $this->_userElements[] = array($element, $name, $options);
        } else {
            throw new Exception(sprintf("Element must be instance of %s", '\ZendY\Form\Element\Widget'));
        }
        return $this;
    }

    /**
     * Obsługa zdarzenia dołączenia nawigatora do formularza nadrzędnego
     * 
     * @return \ZendY\Db\Form\Container\Navigator
     */
    public function onContain() {
        $this->_addDataControls();
        foreach ($this->_userElements as $element) {
            parent::addElement($element[0], $element[1], $element[2]);
        }
        $this->removeAttrib('method');
        $this->refreshDecorators();
        return $this;
    }

    /**
     * Zwraca przycisk dla podanej akcji
     * 
     * @param string $action
     * @return \ZendY\Db\Form\Element\Button
     */
    public function getActionButton($action) {
        return $this->getElement($this->getName() . '_' . $action);
    }

}
