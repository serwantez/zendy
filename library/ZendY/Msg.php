<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

/**
 * Zarządzanie komunikatami programistycznymi wysyłanymi do przeglądarki
 *
 * @author Piotr Zając
 */
class Msg extends Object {

    /**
     * Tablica komunikatów
     * 
     * @var array
     */
    protected static $_messages = array();

    /**
     * Zwraca aktualny czas z dokładnością do mikrosekundy
     * 
     * @return float
     */
    private static function getMicrotime() {
        //pobiera unixowy znacznik czasu
        list($microsec, $sec) = explode(" ", microtime());
        return ((float) $microsec + (float) $sec);
    }

    /**
     * Dodaje komunikat
     * 
     * @param string $msg
     * @return void
     */
    public static function add($msg) {
        self::$_messages[] = array('msg' => $msg, 'time' => self::getMicrotime());
    }

    /**
     * Zwraca wszystkie komunikaty
     * 
     * @return array
     */
    public static function getMessages() {
        return self::$_messages;
    }

    /**
     * Wyświetla wszystkie komunikaty
     * 
     * @return void
     */
    public static function render() {
        print_r(self::$_messages);
    }

    /**
     * Zwraca najdłuższe operacje
     * 
     * @return array
     */
    public static function getLongest() {
        foreach (self::$_messages as $i => $msg) {
            if ($i > 0) {
                $dt[$i] = $msg['time'] - self::$_messages[$i - 1]['time'];
            }
            else
                $dt[$i] = 0;
        }
        $dt = array_filter($dt, function($var) {
                    return $var > 0.3;
                });
        return $dt;
    }

}