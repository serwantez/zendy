<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\Form\Element;

use ZendY\Db\DataInterface;
use ZendY\Css;

/**
 * Kontrolka wyświetlająca wartość wyrażenia związanego ze zbiorem danych
 *
 * @author Piotr Zając
 */
class Expr extends \ZendY\Form\Element\CustomEdit implements DataInterface {

    use \ZendY\Db\DataTrait;

    /**
     * Wyrażenie związane ze zbiorem danych
     * 
     * @var string
     */
    protected $_expr;

    /**
     * Licznik instancji
     * 
     * @var int 
     */
    static protected $count = 0;

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->addClasses(array(
            Css::EDIT,
            Css::TEXT_ALIGN_HORIZONTAL_CENTER,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));        
    }

    /**
     * Ustawia wyrażenie
     * 
     * @param string $expr
     * @return \ZendY\Db\Form\Element\Expr
     */
    public function setExpr($expr) {
        $this->_expr = $expr;
        if ($this->hasDataSource())
            $this->getDataSource()->refreshStateControl($this);
        return $this;
    }

    /**
     * Zwraca wyrażenie
     * 
     * @return string
     */
    public function getExpr() {
        return $this->_expr;
    }

    /**
     * Ładuje domyślne dekoratory
     * 
     * @return \ZendY\Db\Form\Element\Expr
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->setDecorators(array(
                array('Tooltip'),
                array('UiWidgetElement'),
                array('HtmlTag', array('tag' => 'div', 'style' => 'display: inline-block'))
            ))
            ;
        }
        return $this;
    }

    /**
     * Renderuje kontrolkę
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->hasDataSource())
            $this->getDataSource()->addStateControl($this);
        return parent::render($view);
    }

    /**
     * Renderuje kod js odpowiedzialny za dostarczanie danych do kontrolki
     * 
     * @return string
     */
    public function renderDbState() {
        $js = sprintf(
                'ds.addExpr("%s",%s);'
                , $this->getId()
                , \ZendY\JQuery::encodeJson($this->getExpr())
        );
        return $js;
    }

}