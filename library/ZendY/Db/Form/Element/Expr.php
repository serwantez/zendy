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

    const PROPERTY_DATASOURCE = 'dataSource';
    const PROPERTY_DATAEXPR = 'expr';

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_DATAEXPR,
        self::PROPERTY_DATASOURCE,
        self::PROPERTY_ALIGN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_DISABLED,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_LABEL,
        self::PROPERTY_NAME,
        self::PROPERTY_READONLY,
        self::PROPERTY_REQUIRED,
        self::PROPERTY_TITLE,
        self::PROPERTY_TOOLTIP,
        self::PROPERTY_VALUE,
        self::PROPERTY_WIDTH
    );

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
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->addClasses(array(
            Css::EDIT,
            Css::TEXT_ALIGN_HORIZONTAL_CENTER,
            Css::WIDGET,
            Css::WIDGET_CONTENT,
            Css::CORNER_ALL
        ));
        $this->setWidth(30);
    }
    
    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setMaxLength($maxlength) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }    
    
    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setPlaceHolder($placeHolder) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
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