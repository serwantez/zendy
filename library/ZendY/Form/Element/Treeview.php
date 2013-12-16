<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka prezentująca dane w postaci struktury drzewiastej
 *
 * @author Piotr Zając
 */
class Treeview extends CustomList {

    use \ZendY\ControlTrait;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'treeview';
        $this->addClasses(array(
            Css::TREEVIEW,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $icons = array(
            'handleExpanded' => Css::ICON_TRIANGLE1SE,
            'handleCollapsed' => Css::ICON_TRIANGLE1E,
            'nodeExpanded' => Css::ICON_FOLDEROPEN,
            'nodeCollapsed' => Css::ICON_FOLDERCOLLAPSED,
            'leaf' => Css::ICON_DOCUMENT,
        );
        $this->setIcons($icons);
        $this->setRegisterInArrayValidator(false);
    }

    /**
     * Ustawia ikony stosowane w kontrolce
     * 
     * @param array $icons
     * @return \ZendY\Form\Element\Treeview
     */
    public function setIcons(array $icons) {
        $this->setJQueryParam('icons', $icons);
        return $this;
    }

    /**
     * Zwraca ikony stosowane w kontrolce
     * 
     * @return array
     */
    public function getIcons() {
        return $this->getJQueryParam('icons');
    }

}
