<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

/**
 * Mapa wyświetlająca listę linii
 *
 * @author Piotr Zając
 */
class LineListMap extends ListMap {

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Inicjalizuje obiekt
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->helper = 'lineListMap';
        $this->setZoom(7);
        $this->setCenter(array(52, 20));
    }

    /**
     * Formatuje kolumny danych według zdefiniowanych dekoratorów
     * 
     * @param array $data
     * @return array
     */
    public function formatData(array $data = array()) {
        //todo
        return $data;
    }

}

