<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Date;

use ZendY\Exception;

/**
 * Okres czasu pomiędzy dwoma datami
 *
 * @author Piotr Zając
 */
class Period extends \ZendY\Object {

    /**
     * Data początku okresu
     * 
     * @var \Zend_Date
     */
    protected $_begin;

    /**
     * Data końca okresu
     * 
     * @var \Zend_Date
     */
    protected $_end;

    /**
     * Konstruktor
     * 
     * @param \Zend_Date|string $begin
     * @param \Zend_Date|string $end
     */
    public function __construct($begin, $end) {
        parent::__construct(null);
        $this->setBegin($begin);
        $this->setEnd($end);
    }

    /**
     * Ustawia datę początku okresu
     * 
     * @param \Zend_Date|string $begin
     * @return \ZendY\Date\Period
     * @throws Exception
     */
    public function setBegin($begin) {
        if (is_string($begin)) {
            $begin = new \Zend_Date($begin);
        } elseif (!$begin instanceof \Zend_Date) {
            throw new Exception('Begin date must be an instance of Zend_Date or string');
        }
        $this->_begin = $begin;
        return $this;
    }

    /**
     * Zwraca datę początku okresu
     * 
     * @return \Zend_Date
     */
    public function getBegin() {
        return $this->_begin;
    }

    /**
     * Ustawia datę końca okresu
     * 
     * @param \Zend_Date|string $end
     * @return \ZendY\Date\Period
     * @throws Exception
     */
    public function setEnd($end) {
        if (is_string($end)) {
            $end = new \Zend_Date($end);
        } elseif (!$end instanceof \Zend_Date) {
            throw new Exception('End date must be an instance of Zend_Date or string');
        }
        $this->_end = $end;
        return $this;
    }

    /**
     * Zwraca datę końca okresu
     * 
     * @return \Zend_Date
     */
    public function getEnd() {
        return $this->_end;
    }

    /**
     * Zwraca lata kalendarzowe, w których zawiera się okres 
     * (całkowicie lub częściowo)
     * 
     * @return array
     */
    public function getYears() {
        //pobranie roku dla początku okresu
        $begin = $this->getBegin()->get(\Zend_Date::YEAR);
        //pobranie roku dla końca okresu
        $end = $this->getEnd()->get(\Zend_Date::YEAR);
        $y = array();
        //iteracja lat
        for ($i = $begin; $i <= $end; $i++) {
            $y[] = $i;
        }
        return $y;
    }

    /**
     * Zwraca liczbę dni w okresie
     * 
     * @return int
     */
    public function getDays() {
        $end = clone $this->getEnd();
        $diff = $end->sub($this->getBegin())->toValue();
        $days = floor($diff / 60 / 60 / 24);
        return $days;
    }

    /**
     * Zwraca łańcuch zawierający datę początku i końca okresu
     * 
     * @return string
     */
    public function __tostring() {
        return $this->getBegin() . ' - ' . $this->getEnd();
    }

}
