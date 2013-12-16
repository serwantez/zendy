<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataInterface;

/**
 * Interfejs dla przycisków generujących akcje na zbiorach
 * 
 * @author Piotr Zając
 */
interface ActionInterface extends DataInterface {

    /**
     * Ustawia akcję przycisku
     * 
     * @param string $dbAction
     */
    public function setDataAction($dataAction);

    /**
     * Zwraca akcję przycisku
     * 
     * @return string 
     */
    public function getDataAction();

    /**
     * Zwraca tablicę parametrów przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontActionParams();
}
