<?php

namespace Application\Model;

use ZendY\Db\DataSet;

/**
 * Zbiór miejscowości Polski wg danych Głównego Urzędu Statystycznego
 *
 * @author Piotr Zając
 */
class Simc extends DataSet\EditableQuery {
    /**
     * Kolumny zbioru
     */

    const COL_WOJ = 'woj';
    const COL_POW = 'pow';
    const COL_GMI = 'gmi';
    const COL_RODZ_GMI = 'rodz_gmi';
    const COL_TERYT = 'teryt';
    const COL_RODZ_MIEJSC = 'rm';
    const COL_RODZ_MIEJSC_NAZWA = 'nazwa_rm';
    const COL_MZ = 'mz';
    const COL_NAZWA = 'nazwa';
    const COL_SYM = 'sym';
    const COL_SYMPOD = 'sympod';
    const COL_STAN_NA = 'stan_na';

    /**
     * Nazwa tabeli
     */
    const TABLE_NAME = 'simc';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $this->_name = self::TABLE_NAME;
        parent::init();
        $this->setPrimary(self::COL_SYM);
        $this->from(array('s' => $this->_name), array(
                    self::COL_WOJ,
                    self::COL_POW,
                    self::COL_GMI,
                    self::COL_RODZ_GMI,
                    self::COL_TERYT,
                    self::COL_RODZ_MIEJSC,
                    self::COL_MZ,
                    self::COL_NAZWA,
                    self::COL_SYM,
                    self::COL_SYMPOD,
                    self::COL_STAN_NA
                ))
                ->joinLeft(array('rm' => 'wmrodz')
                        , sprintf("s.%s = rm.%s", self::COL_RODZ_MIEJSC, 'rm')
                        , array(
                    self::COL_RODZ_MIEJSC_NAZWA => 'nazwa_rm'
                ))
        ;
        $this->sortAction(array('field' => self::COL_NAZWA));
    }

}

