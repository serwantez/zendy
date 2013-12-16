<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

/**
 * Klasa globalna do obsługi ustawień systemowych
 *
 * @author Piotr Zając
 */
class System {
    /**
     * Szerokość paska przewijania
     */

    const SCROLL_WIDTH = 'scroll_width';

    /**
     * Tablica parametrów systemowych
     * 
     * @var array
     */
    public static $params = array(
        self::SCROLL_WIDTH => 18
    );

    /**
     * Zwraca wartość parametru o podanej nazwie
     * 
     * @param string $paramName
     * @return mixed
     */
    public static function getParam($paramName) {
        return self::$params[$paramName];
    }

}