<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

/**
 * Interfejs dla kontrolek wyświetlających dane w postaci struktur drzewiastych
 * 
 * @author Piotr Zając
 */
interface TreeInterface extends ColumnInterface {

    /**
     * Zwraca nazwę pola przechowującego wartość "z lewej"
     * 
     * @return string
     */
    public function getLeftField();

    /**
     * Zwraca nazwę pola przechowującego wartość "z prawej"
     * 
     * @return string
     */
    public function getRightField();

    /**
     * Zwraca nazwę pola przechowującego wartość "głębokości zagnieżdżenia"
     * 
     * @return string
     */
    public function getDepthField();
}
