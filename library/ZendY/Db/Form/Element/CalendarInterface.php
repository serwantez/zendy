<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

/**
 * Interfejs dla kontrolek wyświetlających dane w postaci kalendarza
 * 
 * @author Piotr Zając
 */
interface CalendarInterface {

    /**
     * Ustawia pole daty
     * 
     * @param string $name
     */
    public function setDateField($name);

    /**
     * Zwraca pole daty
     * 
     * @return string 
     */
    public function getDateField();

    /**
     * Odświeża zakres kalendarza
     * 
     * @param array $params
     */
    public function refreshPeriod($params);
}
