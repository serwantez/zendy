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
