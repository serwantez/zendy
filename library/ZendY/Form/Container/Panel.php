<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Container;

/**
 * Klasa panelu - kontenera
 *
 * @author Piotr Zając
 */
class Panel extends Base {

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_NAME,
        self::PROPERTY_SPACE,
        self::PROPERTY_WIDGETCLASS,
        self::PROPERTY_WIDTH
    );

    /**
     * Obsługa zdarzenia dołączenia panelu do formularza nadrzędnego
     * 
     * @return \ZendY\Form\Container\Panel
     */
    public function onContain() {
        $this->removeAttrib('method');
        $this->refreshDecorators();
        return $this;
    }

}
