<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element\Grid\Plugin;

use ZendY\Form\Element\Grid\Plugin\Custom as Plugin;

/**
 * Mechanizm sortowania danych grida po jego kolumnach
 *
 * @author Piotr Zając
 */
class Sorter extends Plugin {

    /**
     * Wywoływane zanim grid wyśle kod do przeglądarki
     * 
     * @return void
     */
    public function preRender() {
        
    }

    /**
     * Wywoływane po tym jak grid wyśle kod do przeglądarki
     * 
     * @return void
     */
    public function postRender() {
        $sortCols = array();
        foreach ($this->getGrid()->getColumns() as $col) {
            if ($col->getSortable()) {
                $sortCols[] = $col->getName();
            }
        }
        $js = sprintf('dc["gr"]["%s"].setSorting(%s, "%s", "%s")'
                , $this->getGrid()->getId()
                , \ZendX_JQuery::encodeJson($sortCols)
                , $this->getGrid()->getListSource()->getId()
                , $this->getGrid()->getListSource()->getFormId());
        $this->addOnLoad($js);
    }

    /**
     * Wywoływane zanim grid wyśle odpowiedź
     * 
     * @return void
     */
    public function preResponse() {
        
    }

    /**
     * Wywoływane po tym jak grid wyśle odpowiedź
     * 
     * @return void
     */
    public function postResponse() {
        
    }

}