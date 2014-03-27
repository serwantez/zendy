<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

/**
 * Interfejs dla kontrolek zawierających listy danych
 * 
 * @author Piotr Zając
 */
interface ListInterface extends CellInterface {

    /**
     * Ustawia parametry listy
     * 
     * @param array $lists 
     */
    public function setLists(array $lists);

    /**
     * Zwraca parametry listy
     * 
     * @return array
     */
    public function getLists();

    /**
     * Dodaje pojedynczą listę
     * 
     * @param array $list
     * @param string $type
     */
    public function addList(array $list, $type = 'standard');

    /**
     * Usuwa wybraną listę
     * 
     * @param string $type
     */
    public function removeList($type);

    /**
     * Ustawia wyświetlane pola listy
     * 
     * @param string|array $name
     */
    //public function setListField($name);

    /**
     * Zwraca wyświetlane pola listy
     * 
     * @return string|array 
     */
    //public function getListField();

    /**
     * Ustawia pola klucza listy
     * 
     * @param string|array $name
     */
    //public function setKeyField($name);

    /**
     * Zwraca kolumny klucza listy
     * 
     * @return string|array
     */
    //public function getKeyField();

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
    public function getConditionalRowFormats();

    /**
     * Zwraca pola ze zbioru danych potrzebne do wyrenderowania kontrolki
     * 
     * @param string $source 
     * @return array
     */
    public function getFields($list = 'standard');

    /**
     * Formatuje kolumny danych według zdefiniowanych dekoratorów
     * 
     * @param array $data
     * @return array
     */
    public function formatData(array $data = array());
}
