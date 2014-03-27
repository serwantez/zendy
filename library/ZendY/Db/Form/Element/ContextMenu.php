<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\ActionManager;

/**
 * Menu kontekstowe, którego elementy zapisane są w zbiorze danych
 *
 * @author Piotr Zając
 */
class ContextMenu extends \ZendY\Form\Element\ContextMenu {

    use \ZendY\Db\DataTrait;

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
        self::PROPERTY_CLASSES,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_CONTEXT,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
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
     * Akcje menu
     * 
     * @var array
     */
    protected $_dataActions;

    /**
     * Elementy menu
     * 
     * @var array of ZendY\Db\Form\Element\ContextMenuItem
     */
    protected $_items;

    /**
     * Ustawia akcje elementów menu
     * 
     * @param array $dataActions
     * @return \ZendY\Db\Form\Element\ContextMenu
     */
    public function setDataActions(array $dataActions) {
        $this->_dataActions = $dataActions;
        foreach ($dataActions as $key => $value) {
            if (!is_array($value)) {
                $nvalue['action'] = $value;
                $value = $nvalue;
            }
            if (isset($this->_dataSource) && $this->_dataSource->getDataSet()->isRegisteredAction($value['action'])) {
                $params = $this->_dataSource->getDataSet()->getActionParams($value['action']);
                $value['label'] = $params['text'];
                $value['icon'] = $params['icon'];
                $value['frontParam'] = array(
                    'actionType' => $params['type'],
                    'dataAction' => $value['action'],
                    'type' => 'mi'
                );
                $resource = $this->_dataSource->getDataSet()->getName();
                $privilege = $this->_dataSource->getDataSet()->getActionPrivilege($value['action']);
                if (ActionManager::allowed($resource, $privilege)) {
                    $this->addMultiOption($key, $value);
                    $id = $this->getName() . '_' . $value['action'];
                    $this->_items[$id] = new ContextMenuItem($id);
                }
            }
        }
        return $this;
    }

    /**
     * Zwraca akcje elementów menu
     * 
     * @return array
     */
    public function getDataActions() {
        return $this->_dataActions;
    }

    /**
     * Zwraca pojedynczy element menu
     * 
     * @param string $action
     * @return ZendY\Db\Form\Element\ContextMenuItem | null
     */
    public function getItem($action) {
        if (array_key_exists($this->getName() . '_' . $action, $this->_items))
            return $this->_items[$this->getName() . '_' . $action];
        else
            return null;
    }

    /**
     * Renderuje element sprawdzając uprawnienia
     * 
     * @param \Zend_View_Interface $view
     * @return null|string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasDataSource())
            $this->getDataSource()->addNaviControl($this);
        return parent::render($view);
    }

    /**
     * Renderuje kod js odpowiedzialny za dostarczanie danych do kontrolki
     * 
     * @param string $list
     * @return string
     */
    public function renderDbNavi($list = 'standard') {
        $js = array();
        foreach ($this->multiOptions as $key => $option) {
            $js[] = sprintf('ds.addAction("%s",%s);'
                    , $this->getName() . '_' . $option['action']
                    , \ZendY\JQuery::encodeJson($option['frontParam']));
        }
        return implode("\n", $js);
    }

}
