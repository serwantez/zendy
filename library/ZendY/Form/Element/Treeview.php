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
     * Tablica właściwości
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_COLUMNSPACE,
        self::PROPERTY_CONDITIONALROWFORMATS,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_MULTIOPTIONS,
        self::PROPERTY_NAME,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );    

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
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
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setEmptyValue($empty = true) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
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
