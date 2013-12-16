<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataInterface;

/**
 * Interfejs dla kontrolek wyświetlających dane pojedynczego pola 
 * z pojedynczego (bieżącego) rekordu
 * 
 * @author Piotr Zając
 */
interface CellInterface extends DataInterface {

    /**
     * Ustawia nazwę pola
     * 
     * @param string $dataField
     */
    public function setDataField($dataField);

    /**
     * Zwraca nazwę pola z tabeli
     * 
     * @return string 
     */
    public function getDataField();

    /**
     * Zwraca tablicę parametrów przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams();
}
