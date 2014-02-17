<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Container;

use ZendY\Form\Container\Dialog;
use ZendY\Db\DataSet\Editable;
use ZendY\Css;
use ZendY\Db\DataInterface;
use ZendY\Db\Form\Container\Navigator;

/**
 * Okno dialogowe do wykonywania akcji edycyjnych na zbiorze
 *
 * @author Piotr Zając
 */
class EditDialog extends Dialog implements DataInterface {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_ACTIONS = 'actions';
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
        self::PROPERTY_ACTIONS,
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
        $this->_navigator = new Navigator();
        $actions = array(
            array('action' => Editable::ACTION_ADD, 'text' => true),
            array('action' => Editable::ACTION_EDIT, 'text' => true),
            array('action' => Editable::ACTION_SAVE, 'text' => true, 'shortkey' => 'Ctrl+S'),
            array('action' => Editable::ACTION_CANCEL, 'text' => true),
            array('action' => Editable::ACTION_DELETE, 'text' => true),
        );
        $this->_navigator->setActions($actions);

        $this->setModal(true)
                ->setJQueryParam(self::PARAM_RESIZABLE, false)
                ->setJQueryParam(self::PARAM_CLOSEONESCAPE, false)
        ;
    }

    /**
     * Ustawia źródło danych
     * 
     * @param \ZendY\Db\DataSource|null $dataSource
     * @return \ZendY\Db\Form\Container\EditDialog
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
     * Ustawia przyciski akcji
     * 
     * @param array $actions
     * @return \ZendY\Db\Form\Container\EditDialog
     */
    public function setActions(array $actions) {
        $this->_navigator->setActions($actions);
        return $this;
    }

    /**
     * Zwraca przyciski akcji
     * 
     * @return array
     */
    public function getActions() {
        return $this->_navigator->getActions();
    }

    /**
     * Obsługa zdarzenia dołączenia nawigatora do formularza nadrzędnego
     * 
     * @return \ZendY\Db\Form\Container\EditDialog
     */
    public function onContain() {
        $this->_navigator
                ->setDataSource($this->_dataSource)
                ->setAlign(Css::ALIGN_BOTTOM)
        ;
        $this->addContainer($this->_navigator);

        $btnSave = $this->_navigator->getElement($this->_navigator->getName() . '_' . Editable::ACTION_SAVE);
        $btnDel = $this->_navigator->getElement($this->_navigator->getName() . '_' . Editable::ACTION_DELETE);
        $btnCan = $this->_navigator->getElement($this->_navigator->getName() . '_' . Editable::ACTION_CANCEL);

        if (isset($btnSave) && $this->getDataSource()->getDataSet()->getEditMode())
            $this->addCloser($btnSave);
        if (isset($btnDel))
            $this->addCloser($btnDel);
        if (isset($btnCan)) {
            $this->addCloser($btnCan);
            //obsługa automatycznego wykonania akcji cancel przy zamykaniu okna
            $createEvent = sprintf('$("#%s")
            .parent()
            .find("button.ui-dialog-titlebar-close")
            .bind("click", function() {
            $("#%s").click();
            });
            '
                    , $this->getName()
                    , $btnCan->getName()
            );
            $this->setJQueryParam(
                    self::PARAM_EVENT_CREATE
                    , $createEvent
            );
        }

        $this->removeAttrib('method');
        $this->refreshDecorators();
        return $this;
    }

}
