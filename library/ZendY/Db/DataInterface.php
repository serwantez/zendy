<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

use ZendY\Db\DataSource;

/**
 * Interfejs dla kontrolek bazodanowych formularzy i raportów
 * 
 * @author Piotr Zając
 */
interface DataInterface {

    /**
     * Ustawia źródło danych
     * 
     * @param \ZendY\Db\DataSource|null $dataSource
     */
    public function setDataSource(&$dataSource);

    /**
     * Zwraca źródło danych
     * 
     * @return \ZendY\Db\DataSource
     */
    public function getDataSource();
}
