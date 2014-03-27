<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataSource;
use ZendY\Db\DataSet\TreeSetInterface;
use ZendY\Exception;

/**
 * Cecha kontrolek wyświetlających pola drzewiastego zbioru danych
 *
 * @author Piotr Zając
 */
trait TreeTrait {
    
    use ListTrait;

    /**
     * Pole ikony przechowujące nazwę klasy css
     * 
     * @var string
     */
    protected $_iconField;

    /**
     * Pole przechowujące wartość "z lewej"
     * 
     * @var string 
     */
    protected $_leftField;

    /**
     * Pole przechowujące wartość "z prawej"
     * 
     * @var string 
     */
    protected $_rightField;

    /**
     * Pole przechowujące wartość "głębokości zagnieżdżenia"
     * 
     * @var string 
     */
    protected $_depthField;

    /**
     * Ustawia źródło listy
     * 
     * @param \ZendY\Db\DataSource|null $listSource
     * @return \ZendY\Db\Form\Element\TreeInterface
     * @throws Exception
     */
    public function setListSource(&$listSource) {
        if ($listSource instanceof DataSource) {
            $listSource->addNaviControl($this);
            if ($listSource->getDataSet() instanceof TreeSetInterface) {
                $this->_lists['standard']['listSource'] = $listSource;
                $this->_leftField = $listSource->getDataSet()->getLeftField();
                $this->_rightField = $listSource->getDataSet()->getRightField();
                $this->_depthField = $listSource->getDataSet()->getDepthField();
            } else {
                throw new Exception('Instance of TreeInterface must implement TreeSetInterface');
            }
        }
        return $this;
    }

    /**
     * Ustawia pole ikony
     * 
     * @param string $iconField
     * @return \ZendY\Db\Form\Element\TreeInterface
     */
    public function setIconField($iconField) {
        $this->_iconField = $iconField;
        return $this;
    }

    /**
     * Zwraca nazwę pola ikony
     * 
     * @return string
     */
    public function getIconField() {
        return $this->_iconField;
    }

    /**
     * Zwraca nazwę pola przechowującego wartość "z lewej"
     * 
     * @return string
     */
    public function getLeftField() {
        return $this->_leftField;
    }

    /**
     * Zwraca nazwę pola przechowującego wartość "z prawej"
     * 
     * @return string
     */
    public function getRightField() {
        return $this->_rightField;
    }

    /**
     * Zwraca nazwę pola przechowującego wartość "głębokości zagnieżdżenia"
     * 
     * @return string 
     */
    public function getDepthField() {
        return $this->_depthField;
    }

    /**
     * Renderuje kod js odpowiedzialny za dostarczanie danych do kontrolki
     * 
     * @param string $list
     * @return string
     */
    public function renderDbNavi($list = 'standard') {
        $js = sprintf(
                'ds.addNavi("%s",%s);'
                , $this->getId()
                , \ZendY\JQuery::encodeJson($this->getFrontNaviParams($list))
        );
        return $js;
    }

}
