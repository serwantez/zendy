<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

/**
 * Cecha kontrolek korzystających z pojedynczego pola zbioru danych
 *
 * @author Piotr Zając
 */
trait CellTrait {

    use \ZendY\Db\DataTrait;

    /**
     * Nazwa kolumny zbioru
     * 
     * @var string
     */
    protected $_dataField;

    /**
     * Ustawia nazwę pola
     * 
     * @param string $fieldName
     * @return \ZendY\Db\Form\Element\Calendar
     */
    public function setDataField($dataField) {
        $this->_dataField = $dataField;
        if ($this->hasDataSource())
            $this->getDataSource()->refreshEditControl($this);
        return $this;
    }

    /**
     * Zwraca nazwę pola z tabeli
     * 
     * @return string 
     */
    public function getDataField() {
        return $this->_dataField;
    }

    /**
     * Renderuje kod js odpowiedzialny za dostarczanie danych do kontrolki
     * 
     * @return string
     */
    public function renderDbCell() {
        $params = $this->getFrontEditParams();
        if ($this instanceof Element\PresentationInterface) {
            $params['presentation'] = true;
        } else {
            $params['presentation'] = false;
        }
        $js = sprintf(
                'ds.addEdit("%s",%s);'
                , $this->getId()
                , \ZendY\JQuery::encodeJson($params)
        );
        return $js;
    }

}
