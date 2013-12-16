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
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->helper = 'link';
        $this->addClasses(array(Css::LINK));
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
