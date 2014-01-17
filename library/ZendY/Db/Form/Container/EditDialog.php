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
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->_navigator = new Navigator();
        $actions = array(
            array('action' => Editable::ACTION_ADD, 'text' => true),
            array('action' => Editable::ACTION_EDIT, 'text' => true),
            array('action' => Editable::ACTION_SAVE, 'text' => true, 'shortkey' => 'Ctrl+S'),
            array('action' => Editable::ACTION_CANCEL, 'text' => true),
            array('action' => Editable::ACTION_DELETE, 'text' => true),
        );
        $this->_navigator->setActions($actions);

        parent::init();
        $this->setModal(true)
                ->setJQueryParam(self::PARAM_RESIZABLE, false)
                ->setJQueryParam(self::PARAM_CLOSEONESCAPE, false)
        ;
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

        //$btnSave = $this->_navigator->getElement($this->_navigator->getId() . '_' . Editable::ACTION_SAVE);
        $btnDel = $this->_navigator->getElement($this->_navigator->getId() . '_' . Editable::ACTION_DELETE);
        $btnCan = $this->_navigator->getElement($this->_navigator->getId() . '_' . Editable::ACTION_CANCEL);

        /*if (isset($btnSave))
            $this->addCloser($btnSave);*/
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
                    , $this->getId()
                    , $btnCan->getId()
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
