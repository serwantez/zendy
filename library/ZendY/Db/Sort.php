<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

use ZendY\Object;

/**
 * Pomocnik sortowania zbioru
 *
 * @author Piotr Zając
 */
class Sort extends Object {

    /**
     * Reguły sortujące zapisane w tablicy postaci: 
     * array(
     *  array(
     *   'field'=>'sortField1', 
     *   'direction'=>'ASC', 
     *   'case'=>TRUE
     *  ), 
     *  array(
     *   'field'=>'sortField2', 
     *   'direction'=>'DESC', 
     *   'case'=>FALSE
     *  )
     * )
     * 
     * @var array 
     */
    protected $_sorts = array();

    /**
     * Konstruktor
     * 
     * @param array $sorts
     * @return void
     */
    public function __construct(array $sorts = array()) {
        parent::__construct();

        if (isset($sorts)) {
            $this->setSorts($sorts);
        }
    }

    /**
     * Ustawia reguły sortowania
     * 
     * @param array $sorts
     * @return \ZendY\Db\Sort
     */
    public function setSorts(array $sorts) {
        foreach ($sorts as $sort) {
            $this->setSort($sort);
        }
        return $this;
    }

    /**
     * Dodaje pojedynczą regułę sortowania
     * 
     * @param array $sort
     * @return \ZendY\Db\Sort
     */
    public function setSort($sort) {
        $key = false;
        foreach ($this->_sorts as $tempkey => $value) {
            if ($sort['field'] == $value['field']) {
                $key = $tempkey;
                break;
            }
        }

        if ($key === false)
            $this->_sorts[] = $sort;
        else
            $this->_sorts[$key] = $sort;
        return $this;
    }

    /**
     * Zwraca reguły sortujące
     * 
     * @return array
     */
    public function getSorts() {
        return $this->_sorts;
    }

    /**
     * Usuwa reguły sortujące
     * 
     * @return \ZendY\Db\Sort
     */
    public function clearSort() {
        $this->_sorts = array();
        return $this;
    }

    /**
     * Usuwa regułę sortującą dla danego pola
     * 
     * @param string $field
     * @return \ZendY\Db\Sort
     */
    public function removeSort($field) {
        $key = false;
        foreach ($this->_sorts as $tempkey => $value) {
            if ($field == $value['field']) {
                $key = $tempkey;
                break;
            }
        }

        if ($key !== false)
            unset($this->_sorts[$key]);
        return $this;
    }

    /**
     * Zwraca reguły sortujące w postaci tablicy ciągów "order" dla zapytania sql
     * 
     * @param array $columns kolumny z obiektu zapytania sql
     * @return array
     */
    public function toSelect(array $columns = array()) {
        $res = array();
        foreach ($this->_sorts as $sort) {
            //sprawdzenie czy kolumna nie jest aliasem
            foreach ($columns as $columnData) {
                if ($columnData[2] == $sort['field']
                        || ($sort['field'] == $columnData[1] && is_null($columnData[2]))) {
                    if ($columnData[1] instanceof \Zend_Db_Expr) {
                        $sort['field'] = $columnData[1];
                    } else {
                        $sort['field'] = $columnData[0] . '.' . $columnData[1];
                    }
                    break;
                }
            }
            $res[] = $sort['field'] . ' ' . $sort['direction'];
        }

        return $res;
    }

}
