<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report;

use ZendY\Report;
use ZendY\Report\Element\Band;
use ZendY\Report\Element\Band\Column;
use ZendY\Report\Element\Caption;
use ZendY\Css;

/**
 * Przykładowy raport drukujący zbiór danych
 *
 * @author Piotr Zając
 */
class PrintDataSet extends Report {

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        $title = new Caption('title');
        $title->setAttrib('class', Css::REPORT_TITLE);
        $title->setValue('Dataset Report');

        $subtitle = new Caption('subtitle');
        $subtitle->setAttrib('class', Css::REPORT_SUBTITLE);
        $subtitle->setValue($this->_dataSource->getDataSet()->getId());

        $band = new Band('main');
        $fields = $this->_dataSource->getDataSet()->getColumns();
        foreach ($fields as $key => $field) {
            $band->addColumn(new Column(
                            $field
                            , array(
                    )));
        }

        $this->addElements(array($title, $subtitle, $band));
    }

}
