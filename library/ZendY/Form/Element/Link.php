<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

use ZendY\Css;

/**
 * Link na formularzu
 *
 * @author Piotr Zając
 */
class Link extends Widget {

    use \ZendY\ControlTrait;
    
    /**
     * Właściwości komponentu
     */
    const PROPERTY_HREF = 'href';
    
    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_HREF,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_WIDTH
    );    

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->helper = 'link';
        $this->addClasses(array(Css::LINK));
    }
    
    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setDisabled($disabled) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    } 
    
    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setReadOnly($readOnly) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }    

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setRequired($flag = true) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }    
    /**
     * Ustawia adres linku
     * 
     * @param sring $href
     * @return \ZendY\Form\Element\Link
     */
    public function setHref($href) {
        $this->setAttrib('href', $href);
        return $this;
    }

    /**
     * Zwraca adres linku
     * 
     * @return string
     */
    public function getHref() {
        return $this->getAttrib('href');
    }

    /**
     * Zwraca etykietę linku
     * 
     * @return string
     */
    public function getValue() {
        $translator = $this->getTranslator();
        if (null !== $translator) {
            return $translator->translate($this->_value);
        }

        return $this->_value;
    }
    
    /**
     * Ładuje domyślne dekoratory
     * 
     * @return \ZendY\Form\Element\Link
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->setDecorators(array(
                array('Tooltip'),
                array('UiWidgetElement'),
                array('HtmlTag', array('tag' => 'div', 'style' => 'display: inline-block'))
            ))
            ;
        }
        return $this;
    }

}
