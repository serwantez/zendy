<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

/**
 * Interfejs dla kontrolek wyświetlających dane 
 * wszystkich rekordów zbioru z wybranych kolumn
 * 
 * @author Piotr Zając
 */
interface ColumnInterface extends CellInterface {

    /**
     * Ustawia źródło listy
     * 
     * @param \ZendY\Db\DataSource|null $listSource 
     */
    public function setListSource(&$listSource);

    /**
     * Zwraca źródło listy
     * 
     * @return \ZendY\Db\DataSource 
     */
    public function getListSource();

    /**
     * Ustawia wyświetlane pola listy
     * 
     * @param string|array $name
     */
    public function setListField($name);

    /**
     * Zwraca wyświetlane pola listy
     * 
     * @return string|array 
     */
    public function getListField();

    /**
     * Ustawia pola klucza listy
     * 
     * @param string|array $name
     */
    public function setKeyField($name);

    /**
     * Zwraca kolumny klucza listy
     * 
     * @return string|array
     */
    public function getKeyField();

    /**
     * Ustawia statyczne renderowanie listy opcji
     * 
     * @param bool $staticRender
     */
    public function setStaticRender($staticRender = TRUE);

    /**
     * Zwraca informację o tym czy renderowanie listy ma się odbywać statycznie
     * 
     * @return bool
     */
    public function getStaticRender();

    /**
     * Zwraca tablicę parametrów przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontNaviParams();

    /**
     * Zwraca warunki formatujące wiersze
     * 
     * @return array
     */
    public function getConditionalRowFormat();

    /**
     * Zwraca pola ze zbioru danych potrzebne do wyrenderowania kontrolki
     *  
     * @return array
     */
    public function getFields();

    /**
     * Formatuje kolumny danych według zdefiniowanych dekoratorów
     * 
     * @param array $data
     * @return array
     */
    public function formatData(array $data = array());
}
