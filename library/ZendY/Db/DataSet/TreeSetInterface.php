<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

/**
 * Interfejs zbiorów ze strukturą drzewiastą typu NestedSet
 * 
 * @author Piotr Zając
 */
interface TreeSetInterface {

    /**
     * Zwraca pole przechowujące wartość "z lewej"
     * 
     * @return string
     */
    public function getLeftField();

    /**
     * Zwraca pole przechowujące wartość "z prawej"
     * 
     * @return string
     */
    public function getRightField();

    /**
     * Zwraca pole przechowujące wartość "głębokości zagnieżdżenia"
     * 
     * @return string
     */
    public function getDepthField();
}
