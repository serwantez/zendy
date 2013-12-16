<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element\Filter;

/**
 * Interfejs dla kontrolek filtrujących
 * 
 * @author Piotr Zając
 */
interface FilterInterface {

    /**
     * Ustawia operator filtra
     * 
     * @param string $operator
     */
    public function setOperator($operator);

    /**
     * Zwraca operator filtra
     * 
     * @return string
     */
    public function getOperator();

    /**
     * Zwraca tablicę parametrów filtrujących przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontFilterParams();
}
