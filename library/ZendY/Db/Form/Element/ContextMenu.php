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
                $resource = $this->_dataSource->getDataSet()->getId();
                $privilege = $this->_dataSource->getDataSet()->getActionPrivilege($value['action']);
                if (ActionManager::allowed($resource, $privilege)) {
                    $this->addMultiOption($key, $value);
                    $id = $this->getId() . '_' . $value['action'];
                    $this->_items[$id] = new ContextMenuItem($id);
                }
            }
        }
        return $this;
    }

    /**
     * Zwraca pojedynczy element menu
     * 
     * @param string $action
     * @return ZendY\Db\Form\Element\ContextMenuItem | null
     */
    public function getItem($action) {
        if (array_key_exists($this->getId() . '_' . $action, $this->_items))
            return $this->_items[$this->getId() . '_' . $action];
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
     * @return string
     */
    public function renderDbNavi() {
        $js = array();
        foreach ($this->multiOptions as $key => $option) {
            $js[] = sprintf('ds.addAction("%s",%s);'
                    , $this->getId() . '_' . $option['action']
                    , \ZendY\JQuery::encodeJson($option['frontParam']));
        }
        return implode("\n", $js);
    }

}
