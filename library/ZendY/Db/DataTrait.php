<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

use ZendY\Db\DataSource;

/**
 * Cecha kontrolek bazodanowych - obsługa połączeń ze zbiorem danych
 *
 * @author Piotr Zając
 */
trait DataTrait {

    /**
     * Źródło danych
     * 
     * @var \ZendY\Db\DataSource 
     */
    protected $_dataSource;

    /**
     * Ustawia źródło danych
     * 
     * @param \ZendY\Db\DataSource|null $dataSource
     * @return \ZendY\Db\Form\Element\CellInterface
     * @throws \ZendY\Exception
     */
    public function setDataSource(&$dataSource) {
        if ($dataSource instanceof DataSource) {
            $this->_dataSource = $dataSource;
        }

        return $this;
    }

    /**
     * Zwraca źródło danych
     * 
     * @return \ZendY\Db\DataSource|false
     */
    public function getDataSource() {
        return $this->_dataSource;
    }

    /**
     * Czy jest ustawione źródło danych
     * 
     * @return bool
     */
    public function hasDataSource() {
        if (isset($this->_dataSource))
            return true;
        else
            return false;
    }

}
