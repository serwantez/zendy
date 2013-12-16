<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

/**
 * Interfejs tabeli bazodanowej
 * 
 * @author Piotr Zając
 */
interface TableInterface {

    /**
     * Zwraca opis kolumn tabeli w postaci tablicy asocjacyjnej, gdzie kluczami są nazwy kolumn
     * 
     * @return array
     */
    public function describe();

    /**
     * Zwraca informacje o pojedynczym polu tabeli źródłowej
     * 
     * @param string $field
     * @param array|null $describe
     * @return array
     */
    public function describeField($field, $describe = null);

    /**
     * Zwraca nazwę kolumny tabeli źródłowej na podstawie jej aliasu
     * 
     * @param string $alias
     * @return string
     */
    public function getTableField($alias);
}