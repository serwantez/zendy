<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet;
use ZendY\Db\Filter;

/**
 * Zbiór obliczeniowy obchodów kalendarzowych
 *
 * @author Piotr Zając
 */
class Calendar extends DataSet\Query {
    /**
     * Domyślne kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_MOVABILITY = 'movability';
    const COL_NAME = 'name_pl';
    const COL_DAY = 'day';
    const COL_WEIGHT_TYPE = 'weight_type';
    const COL_WEIGHT_TYPE_NAME = 'weight_type_name';
    const COL_WEIGHT_NUMBER = 'weight_number';
    const COL_DEPENDENCY_FUNCTION = 'dependency_function';
    const COL_DEPENDENCY_FUNCTION_NAME = 'dependency_function_name';
    const COL_DEPENDENCY_PARAM = 'dependency_param';
    const COL_HOLIDAY = 'holiday';
    const COL_YEAR_DATE = 'year_date';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'calendar';

    /**
     * Okres czasu, dla którego wyświetlane są dane
     * 
     * @var \ZendY\Date\Period
     */
    protected $_period;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->sortAction(array('field' => self::COL_YEAR_DATE));
        $this->sortAction(array('field' => self::COL_WEIGHT_NUMBER));
        $this->setPrimary(self::COL_ID);
    }

    /**
     * Zwraca zapytanie dla danego roku
     * 
     * @param int $year rok
     * @param \Zend_Date|null $begin data początkowa w podanym roku
     * @param \Zend_Date|null $end data końcowa w podanym roku
     * @return \ZendY\Db\Select
     */
    protected function _getYearSelect($year, $begin = null, $end = null) {
        $yearDate = new \Zend_Db_Expr(sprintf("calendarDate(`%s`, `%s`, `df`.`%s`, `%s`, %s)"
                                , self::COL_MOVABILITY
                                , self::COL_DAY
                                , ListItem::COL_NAME
                                , self::COL_DEPENDENCY_PARAM
                                , $year
                ));
        $select = new \ZendY\Db\Select($this->_db);
        $select->from(array('c' => self::TABLE_NAME), array(
                    self::COL_ID,
                    self::COL_MOVABILITY,
                    self::COL_NAME,
                    self::COL_DAY,
                    self::COL_WEIGHT_TYPE,
                    self::COL_WEIGHT_NUMBER,
                    self::COL_DEPENDENCY_FUNCTION,
                    self::COL_DEPENDENCY_PARAM,
                    self::COL_HOLIDAY,
                    self::COL_YEAR_DATE => $yearDate
                ))
                ->join(array('wt' => ListItem::TABLE_NAME)
                        , sprintf("`c`.`%s` = `wt`.`%s` and `wt`.`%s` = 10"
                                , self::COL_WEIGHT_TYPE
                                , ListItem::COL_ITEM_ID
                                , ListItem::COL_LIST_ID)
                        , array(
                    self::COL_WEIGHT_TYPE_NAME => ListItem::COL_NAME
                ))
                ->joinLeft(array('df' => ListItem::TABLE_NAME)
                        , sprintf("`c`.`%s` = `df`.`%s` and `df`.`%s` = 12"
                                , self::COL_DEPENDENCY_FUNCTION
                                , ListItem::COL_ITEM_ID
                                , ListItem::COL_LIST_ID)
                        , array(
                    self::COL_DEPENDENCY_FUNCTION_NAME => ListItem::COL_NAME
                ))
        ;
        if (isset($begin) || isset($end)) {
            $subSelect = clone $select;
            $select = new \ZendY\Db\Select($this->_db);
            $select->from($subSelect, '*');
            if (isset($begin))
                $select->where(sprintf("`%s` >= ?", self::COL_YEAR_DATE), $begin->toString('YYYY-MM-dd'));
            if (isset($end))
                $select->where(sprintf("`%s` <= ?", self::COL_YEAR_DATE), $end->toString('YYYY-MM-dd'));
        }
        return $select;
    }

    /**
     * Ustawia okres czasu dla zbioru
     * 
     * @param \ZendY\Date\Period $period
     * @return \ZendY\Db\DataSet\App\Calendar
     */
    public function setPeriod(\ZendY\Date\Period $period) {
        $this->_period = $period;
        $years = $this->_period->getYears();
        $begin = $this->_period->getBegin();
        $end = $this->_period->getEnd();
        $days = $this->_period->getDays();
        //filtr ograniczający wyświetlanie świąt o zasięgu lokalnym
        $noLocalFeastFilter = new Filter();
        $noLocalFeastFilter->addFilter(self::COL_WEIGHT_NUMBER, array(4, 8, 11, 14), DataSet\Base::OPERATOR_NOT_IN);
        $noLocalFeastFilterSql = $noLocalFeastFilter->toSelect();

        if (count($years) == 1) {
            $s = $this->_getYearSelect($years[0], $begin, $end);
            if ($days > 7) {
                $s->where($noLocalFeastFilterSql);
            }
            $this->_select = clone $s;
        } else {
            $select = new \ZendY\Db\Select($this->_db);
            $selects = array();
            foreach ($years as $year) {
                $yearBegin = null;
                $yearEnd = null;
                if ($year == $begin->get(\Zend_Date::YEAR)) {
                    $yearBegin = $begin;
                } elseif ($year == $end->get(\Zend_Date::YEAR)) {
                    $yearEnd = $end;
                }
                $s = $this->_getYearSelect($year, $yearBegin, $yearEnd);
                if ($days > 7) {
                    $s->where($noLocalFeastFilterSql);
                }
                $selects[] = $s;
            }
            $select->union($selects);
            $this->_select = new \ZendY\Db\Select($this->_db);
            $this->from(array('u' => new \Zend_Db_Expr('(' . $select . ')')), '*');
        }
        return $this;
    }

    /**
     * Zwraca okres czasu dla zbioru
     * 
     * @return \ZendY\Date\Period
     */
    public function getPeriod() {
        return $this->_period;
    }

    //Funkcje obliczające datę obchodów ruchomych w Kościele Rzymsko-Katolickim

    /**
     * Zwraca datę I Niedzieli Adwentu 
     * w podanym roku kalendarza gregoriańskiego
     * 
     * @param int $year
     * @return \Zend_Date
     */
    public static function getAdventSunday($year) {
        $date = new \Zend_Date(mktime(0, 0, 0, 12, 3, $year));
        $wday = $date->get(\Zend_Date::WEEKDAY_DIGIT);
        $date->addDay(-$wday);
        return $date;
    }

    /**
     * Zwraca datę I Niedzieli Wielkanocnej 
     * dla podanego roku kalendarza gregoriańskiego
     * 
     * @param int $year
     * @return \Zend_Date
     */
    public static function getEasterSunday($year) {
        return new \Zend_Date(easter_date($year));
    }

    /**
     * Zwraca datę I Niedzieli Wielkiego Postu 
     * dla podanego roku kalendarza gregoriańskiego
     * 
     * @param int $year
     * @return \Zend_Date
     */
    public static function getPentecostSunday($year) {
        $date = self::getEasterSunday($year);
        $date->addDay(49);
        return $date;
    }

    /**
     * Zwraca datę Święta Świętej Rodziny 
     * dla podanego roku kalendarza gregoriańskiego.
     * Jest to niedziela w oktawie Bożego Narodzenia, 
     * chyba, że niedziela ta wypada 1 stycznia, 
     * wtedy święto obchodzone jest 30 grudnia.
     * 
     * @param int $year
     * @return \Zend_Date
     */
    public static function getFeastHolyFamily($year) {
        $date = new \Zend_Date(mktime(0, 0, 0, 12, 25, $year));
        $wday = $date->get(\Zend_Date::WEEKDAY_DIGIT);
        if ($wday > 0)
            $date->addDay((7 - $wday) % 7);
        else
            $date->addDay(5);
        return $date;
    }

    /**
     * Zwraca datę Niedzieli Chrztu Pańskiego (I Niedzieli Zwykłej) 
     * dla podanego roku kalendarza gregoriańskiego.
     * Jest to pierwsza niedziela po Uroczystości Objawienia Pańskiego.
     * 
     * @param int $year
     * @return \Zend_Date
     */
    public static function getFirstOrdinarySunday($year) {
        $date = new \Zend_Date(mktime(0, 0, 0, 1, 7, $year));
        $wday = $date->get(\Zend_Date::WEEKDAY_DIGIT);
        $date->addDay((7 - $wday) % 7);
        return $date;
    }

    /**
     * Zwraca datę I Niedzieli Wielkiego Postu 
     * dla podanego roku kalendarza gregoriańskiego
     * 
     * @param int $year
     * @return \Zend_Date
     */
    public static function getFirstGreatLentSunday($year) {
        $date = self::getEasterSunday($year);
        $date->addDay(-42);
        return $date;
    }

    /**
     * Zwraca datę Środy Popielcowej 
     * dla podanego roku kalendarza gregoriańskiego
     * 
     * @param int $year
     * @return \Zend_Date
     */
    public static function getAshWednesday($year) {
        $date = self::getEasterSunday($year);
        $date->addDay(-46);
        return $date;
    }

    /**
     * Zwraca daty wszystkich niedziel Okresu Zwykłego 
     * dla podanego roku kalendarza gregoriańskiego
     * 
     * @param int $year
     * @return array
     */
    public static function getOrdinarySundays($year) {
        $sundays = array();
        //niedziele zwykłe przed środą popielcową
        $ashWednesday = self::getAshWednesday($year);
        $sunday = self::getFirstOrdinarySunday($year);
        $i = 0;
        while ($sunday < $ashWednesday) {
            $i++;
            $sundays[$i] = $sunday->toString();
            $sunday->addDay(7);
        }

        //Zesłanie Ducha Św.
        $pentecost = self::getPentecostSunday($year);

        $sunday = self::getAdventSunday($year);
        //Niedziela Chrystusa Króla
        $sunday->addDay(-7);

        //niedziele zwykłe po wielkanocy
        $i = 35;
        while ($sunday > $pentecost) {
            $i--;
            $sundays[$i] = $sunday->toString();
            $sunday->addDay(-7);
        }
        return $sundays;
    }

    /**
     * Zwraca datę ostatniej niedzieli października
     * (Rocznica Poświęcenia Własnego Kościoła)
     * 
     * @param int $year
     * @return \Zend_Date
     */
    public static function getLastOctoberSunday($year) {
        $date = new \Zend_Date(mktime(0, 0, 0, 10, 31, $year));
        $wday = $date->get(\Zend_Date::WEEKDAY_DIGIT);
        $date->addDay(-$wday);
        return $date;
    }

    /**
     * Zwraca zapytanie tworzące w bazie MySQL 
     * funkcję obliczającą datę Wielkanocy
     * 
     * @return string
     */
    public static function createFunctionEasterSunday() {
        return "CREATE FUNCTION `easterSunday`(`inYear` YEAR, `param` SMALLINT) RETURNS date
    DETERMINISTIC
    BEGIN
    DECLARE a, b, c, d, e, k, m, n, p, q INT;

    DECLARE easter DATE;

    SET k = FLOOR(inYear / 100);
    SET a = MOD(inYear, 19);
    SET b = MOD(inYear, 4);
    SET c = MOD(inYear, 7);
    SET q = FLOOR(k / 4);
    SET p = FLOOR((13 + 8 * k) / 25);
    SET m = MOD((15-p+k-q), 30);
    SET d = MOD((19 * a + m), 30);
    SET n = MOD((4+k-q), 7);
    SET e = MOD((2*b+4*c+6*d+n), 7);

    SET easter = CASE
        WHEN d + e <= 9 THEN CONCAT_WS('-', inYear, '03', 22 + d + e)
        WHEN d = 29 AND e = 6 THEN CONCAT_WS('-', inYear, '04-19')
        WHEN d = 28 AND e = 6 AND a > 10 THEN CONCAT_WS('-', inYear, '04-18')
        ELSE CONCAT_WS('-', inYear, '04', LPAD(d + e - 9, 2, 0))
    END;

    RETURN DATE_ADD(easter, INTERVAL param DAY);
    END";
    }

    /**
     * Zwraca zapytanie tworzące w bazie MySQL
     * funkcję obliczającą datę I Niedzieli Adwentu
     * 
     * @return string
     */
    public static function createFunctionAdventSunday() {
        return "CREATE FUNCTION `adventSunday`(`inYear` YEAR, `param` SMALLINT) RETURNS date
    DETERMINISTIC
    BEGIN
	DECLARE d DATE;
        DECLARE w TINYINT;
        SET d = CONCAT_WS('-', inYear, '12', '03');
        SET w = WEEKDAY(d);
	RETURN DATE_ADD(d, INTERVAL param-(w+1)%7 DAY);
    END";
    }

    /**
     * Zwraca zapytanie tworzące w bazie MySQL 
     * funkcję obliczającą datę święta Świętej Rodziny
     * 
     * @return string
     */
    public static function createFunctionFeastHolyFamily() {
        return "CREATE FUNCTION `feastHolyFamily`(`inYear` YEAR, `param` SMALLINT) RETURNS date
        DETERMINISTIC
        BEGIN
	DECLARE d DATE;
        DECLARE w TINYINT;
        SET d = CONCAT_WS('-', inYear, '12', '25');
        SET w = WEEKDAY(d);
        IF w<6 THEN
        SET d = DATE_ADD(d, INTERVAL 6-w DAY);
        ELSE SET d = DATE_ADD(d, INTERVAL 5 DAY);
        END IF;
	RETURN DATE_ADD(d, INTERVAL param DAY);
        END";
    }

    /**
     * Zwraca zapytanie tworzące w bazie MySQL 
     * funkcję obliczającą datę ostatniej niedzieli października
     * 
     * @return string
     */
    public static function createFunctionLastOctoberSunday() {
        return "CREATE FUNCTION `lastOctoberSunday`(`inYear` YEAR, `param` SMALLINT) RETURNS date
    DETERMINISTIC
    BEGIN
    DECLARE d DATE;
    DECLARE w TINYINT;
    SET d = CONCAT_WS('-', inYear, '10', '31');
    SET w = WEEKDAY(d);
    RETURN DATE_ADD(d, INTERVAL param-((w+1)%7) DAY);
    END";
    }

    /**
     * Zwraca zapytanie tworzące w bazie MySQL 
     * funkcję obliczającą datę Środy Popielcowej
     * 
     * @return string
     */
    public static function createFunctionAshWednesday() {
        return "CREATE FUNCTION `ashWednesday`(`inYear` YEAR, `param` SMALLINT) RETURNS date
    DETERMINISTIC
    BEGIN
    DECLARE d DATE;
    SET d = easterSunday(inYear, 0);
    RETURN DATE_ADD(d, INTERVAL param-46 DAY);
    END";
    }

    /**
     * Zwraca zapytanie tworzące w bazie MySQL 
     * funkcję obliczającą datę I Niedzieli Okresu Zwykłego
     * 
     * @return string
     */
    public static function createFunctionFirstOrdinarySunday() {
        return "CREATE FUNCTION `firstOrdinarySunday`(`inYear` YEAR, `param` SMALLINT) RETURNS date
    DETERMINISTIC
    BEGIN
    DECLARE d DATE;
    DECLARE w TINYINT;
    SET d = CONCAT_WS('-', inYear, '01', '07');
    SET w = WEEKDAY(d);
    RETURN DATE_ADD(d, INTERVAL param+6-w DAY);
    END";
    }

    /**
     * Zwraca zapytanie tworzące w bazie MySQL 
     * funkcję obliczającą datę Zesłania Ducha Świętego
     * 
     * @return string
     */
    public static function createFunctionPentecostSunday() {
        return "CREATE FUNCTION `pentecostSunday`(`inYear` YEAR, `param` SMALLINT) RETURNS date
    DETERMINISTIC
    BEGIN
    DECLARE d DATE;
    SET d = easterSunday(inYear, 0);
    RETURN DATE_ADD(d, INTERVAL param+49 DAY);
    END";
    }

    /**
     * Zwraca zapytanie tworzące w bazie MySQL 
     * funkcję obliczającą datę wszystkich niedziel okresu zwykłego
     * 
     * @return string
     */
    public static function createFunctionOrdinarySunday() {
        return "CREATE FUNCTION `ordinarySunday`(`inYear` YEAR, `param` SMALLINT) RETURNS date
    DETERMINISTIC
    BEGIN
    DECLARE d, ash, p, a DATE;
    DECLARE wn1, wn2 TINYINT;
    SET d = firstOrdinarySunday(inYear, 0);
    SET ash = ashWednesday(inYear, 0);
    SET p = pentecostSunday(inYear, 0);
    SET a = adventSunday(inYear, 0);
    SET wn1 = CEIL(DATEDIFF(ash, d)/7);
    SET wn2 = 36-ROUND(DATEDIFF(a, p)/7);
    IF param BETWEEN 1 AND wn1 THEN
    RETURN DATE_ADD(d, INTERVAL (param-1)*7 DAY);
    ELSEIF param BETWEEN wn2 AND 34 THEN
    RETURN DATE_ADD(p, INTERVAL (param-wn2+1)*7 DAY);
    ELSE
    RETURN NULL;
    END IF;
    END";
    }

}