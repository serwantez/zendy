<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Kontrolka tekstowa z ikoną
 *
 * @author Piotr Zając
 */
class IconEdit extends CustomEdit {

    use \ZendY\ControlTrait;

    /**
     * Właściwości komponentu
     */

    const PROPERTY_ICON = 'icon';
    const PROPERTY_ICON_POSITION = 'iconPosition';

    /**
     * Parametry
     */
    const PARAM_ICON = 'icon';
    const PARAM_POSITION = 'position';

    /**
     * Pozycje ikony
     */
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_LABEL,
        self::PROPERTY_ICON,
        self::PROPERTY_ICON_POSITION,
        self::PROPERTY_MAXLENGTH,
        self::PROPERTY_PLACEHOLDER,
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
        $this->helper = 'iconEdit';
        $this->addClasses(array(
            Css::ICONEDIT,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setIconPosition(self::POSITION_RIGHT);
    }

    /**
     * Ustawia klasę ikony
     * 
     * @param string $icon
     * @param string|null $position
     * @return \ZendY\Form\Element\IconEdit
     */
    public function setIcon($icon, $position = null) {
        $this->jQueryParams[self::PARAM_ICON] = $icon;
        if (isset($position)) {
            $this->setIconPosition($position);
        }
        return $this;
    }

    /**
     * Zwraca klasę ikony
     * 
     * @return string
     */
    public function getIcon() {
        return $this->jQueryParams[self::PARAM_ICON];
    }

    /**
     * Ustawia pozycję ikony względem pola tekstowego
     * 
     * @param string $position
     * @return \ZendY\Form\Element\IconEdit
     */
    public function setIconPosition($position) {
        $this->jQueryParams[self::PARAM_POSITION] = $position;
        $this->addClass(Css::ICONEDIT . '-' . $position);
        $this->removeClass(Css::ICONEDIT . '-' . ($position == self::POSITION_LEFT ? self::POSITION_RIGHT : self::POSITION_LEFT));
        return $this;
    }

    /**
     * Zwraca pozycję ikony względem pola tekstowego
     * 
     * @return string
     */
    public function getIconPosition() {
        return $this->jQueryParams[self::PARAM_POSITION];
    }

}
