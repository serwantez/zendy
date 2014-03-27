<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Container;

use ZendY\Form\Element;
use ZendY\Css;
use ZendY\Exception;

/**
 * Klasa okna dialogowego jako kontenera w formularzu
 *
 * @author Piotr Zając
 * @link http://api.jqueryui.com/dialog
 */
class Dialog extends Base {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_AUTOOPEN = 'autoOpen';
    const PROPERTY_MODAL = 'modal';
    const PROPERTY_OPENERS = 'openers';
    const PROPERTY_TITLE = 'title';

    /**
     * Opcje
     */
    const PARAM_AUTOOPEN = 'autoOpen';
    const PARAM_BUTTONS = 'buttons';
    const PARAM_CLOSEONESCAPE = 'closeOnEscape';
    const PARAM_DRAGGABLE = 'draggable';
    const PARAM_HEIGHT = 'height';
    const PARAM_MAX_HEIGHT = 'maxHeight';
    const PARAM_MAX_WIDTH = 'maxWidth';
    const PARAM_MIN_HEIGHT = 'minHeight';
    const PARAM_MIN_WIDTH = 'minWidth';
    const PARAM_MODAL = 'modal';
    const PARAM_POSITION = 'position';
    const PARAM_RESIZABLE = 'resizable';
    const PARAM_WIDTH = 'width';

    /**
     * metody
     */
    const PARAM_METHOD_CLOSE = 'close';
    const PARAM_METHOD_DESTROY = 'destroy';
    const PARAM_METHOD_ISOPEN = 'isOpen';
    const PARAM_METHOD_MOVETOTOP = 'moveToTop';
    const PARAM_METHOD_OPTION = 'option';
    const PARAM_METHOD_OPEN = 'open';
    const PARAM_METHOD_WIDGET = 'widget';
    /**
     * zdarzenia
     */
    const PARAM_EVENT_BEFORECLOSE = 'beforeClose';
    const PARAM_EVENT_CLOSE = 'close';
    const PARAM_EVENT_CREATE = 'create';
    const PARAM_EVENT_DRAG = 'drag';
    const PARAM_EVENT_DRAGSTART = 'dragStart';
    const PARAM_EVENT_DRAGSTOP = 'dragStop';
    const PARAM_EVENT_FOCUS = 'focus';
    const PARAM_EVENT_OPEN = 'open';
    const PARAM_EVENT_RESIZE = 'resize';
    const PARAM_EVENT_RESIZESTART = 'resizeStart';
    const PARAM_EVENT_RESIZESTOP = 'resizeStop';

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $count = 0;

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_AUTOOPEN,
        self::PROPERTY_CLASSES,
        self::PROPERTY_HEIGHT,
        self::PROPERTY_MODAL,
        self::PROPERTY_NAME,
        self::PROPERTY_OPENERS,
        self::PROPERTY_TITLE,
        self::PROPERTY_WIDTH
    );

    /**
     * Ustawia wartości domyślne
     * 
     * @return void
     */
    protected function _setDefaults() {
        parent::_setDefaults();
        $this->_events = array(
            self::PARAM_EVENT_BEFORECLOSE,
            self::PARAM_EVENT_CLOSE,
            self::PARAM_EVENT_CREATE,
            self::PARAM_EVENT_DRAG,
            self::PARAM_EVENT_DRAGSTART,
            self::PARAM_EVENT_DRAGSTOP,
            self::PARAM_EVENT_FOCUS,
            self::PARAM_EVENT_OPEN,
            self::PARAM_EVENT_RESIZE,
            self::PARAM_EVENT_RESIZESTART,
            self::PARAM_EVENT_RESIZESTOP
        );
        $this->_prepareEventParams();
        $this->setJQueryParam(self::PARAM_AUTOOPEN, FALSE);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setAlign($align) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setWidgetClass($widgetClass) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Zakaz używania metody
     * 
     * @param string $action
     * @throws Exception
     */
    final public function setSpace($space = 2) {
        throw new Exception("You mustn't use method " . __FUNCTION__);
    }

    /**
     * Odświeża dekoratory po zmianach wykonanych na formularzu po dołaczeniu go do innego kontenera
     * 
     * @return \ZendY\Form\Container\Dialog
     */
    public function refreshDecorators() {
        $attribs = \ZendY\View\Helper\Widget::prepareCSS($this->getAttribs());
        $attribs['id'] = $this->getName();
        $this->_prepareRenderEventParams();
        $attribs['jQueryParams'] = $this->getJQueryParams();
        $this->setDecorators(array(
            array('FormElements'),
            array(array('Inner' => 'HtmlTag'), array(
                    'class' => Css::ALIGN_CLIENT . ' ' . Css::PADDING_ALL . ' ' . Css::SCROLL_AUTO
            )),
            array('Dialog', $attribs)
        ));
        return $this;
    }

    /**
     * Ustawia parametr odpowiedzialny za odświeżenie map 
     * znajdujących się w oknie dialogowym
     * 
     * @param \ZendY\Form\Container\Custom $container
     * @return \ZendY\Form\Container\Dialog
     */
    public function refreshHiddenMap(Base $container) {
        $elements = $container->getElements();
        foreach ($elements as $element) {
            if ($element instanceof Element\CustomMap) {
                $this->setJQueryParam(self::PARAM_EVENT_OPEN
                        , sprintf('dc["mp"]["%s"].refresh();'
                                , $element->getId()));
            }
        }
        foreach ($container->getSubForms() as $subform) {
            $this->refreshHiddenMap($subform);
        }
        return $this;
    }

    /**
     * Obsługa zdarzenia dołączenia okna do formularza nadrzędnego
     * 
     * @return \ZendY\Form\Container\Dialog
     */
    public function onContain() {
        $this->removeAttrib('method');
        $this->refreshHiddenMap($this);
        $this->refreshDecorators();
        return $this;
    }

    /**
     * Ustawia tytuł okna
     * 
     * @param string $title
     * @return \ZendY\Form\Container\Dialog
     */
    public function setTitle($title) {
        $this->setJQueryParam('title', $title);
        return $this;
    }

    /**
     * Zwraca tytuł okna
     * 
     * @return string
     */
    public function getTitle() {
        return $this->getJQueryParam('title');
    }

    /**
     * Ustawia czy okno ma być modalne
     * 
     * @param bool|null $modal
     * @return \ZendY\Form\Container\Dialog
     */
    public function setModal($modal = TRUE) {
        $this->setJQueryParam(self::PARAM_MODAL, $modal);
        return $this;
    }

    /**
     * Zwraca informację o tym, czy okno ma być modalne
     * 
     * @return bool
     */
    public function getModal() {
        return $this->getJQueryParam(self::PARAM_MODAL);
    }

    /**
     * Ustawia czy okno ma być otwarte przy uruchamianiu strony
     * 
     * @param bool $modal
     * @return \ZendY_Form_Container_Dialog
     */
    public function setAutoOpen($autoOpen = TRUE) {
        $this->setJQueryParam(self::PARAM_AUTOOPEN, $autoOpen);
        return $this;
    }

    /**
     * Zwraca informację o tym, czy okno ma być otwarte przy uruchamianiu strony
     * 
     * @return bool
     */
    public function getAutoOpen() {
        return $this->getJQueryParam(self::PARAM_AUTOOPEN);
    }

    /**
     * Zwraca kod js wykonania podanej metody
     * 
     * @param string $method
     * @return string
     */
    public function getJQueryMethod($method) {
        $js = sprintf('%s("#%s").dialog("%s");'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $this->getId()
                , $method
        );
        return $js;
    }

    /**
     * Ustawia szerokość okna
     * 
     * @param mixed $value
     * @param string $unit zmienna występuje tylko z powodu kompatybilności 
     * z metodą przesłoniętą
     * @return \ZendY\Form\Container\Dialog
     */
    public function setWidth($value, $unit = 'px') {
        $this->setJQueryParam(self::PARAM_WIDTH, $value);
        return $this;
    }

    /**
     * Zwraca szerokość okna
     * 
     * @return mixed
     */
    public function getWidth() {
        return $this->getJQueryParam(self::PARAM_WIDTH);
    }

    /**
     * Ustawia wysokość okna
     * 
     * @param mixed $value
     * @param string $unit zmienna występuje tylko z powodu kompatybilności 
     * z metodą przesłoniętą
     * @return \ZendY\Form\Container\Dialog
     */
    public function setHeight($value, $unit = 'px') {
        $this->setJQueryParam(self::PARAM_HEIGHT, $value);
        return $this;
    }

    /**
     * Zwraca wysokość okna
     * 
     * @return mixed
     */
    public function getHeight() {
        return $this->getJQueryParam(self::PARAM_HEIGHT);
    }

    /**
     * Ustawia przyciski dialogowe
     * 
     * @param array $buttons
     * @return \ZendY\Form\Container\Dialog
     */
    public function setButtons(array $buttons) {
        foreach ($buttons as $text => $event) {
            $buttons[$text] = new \Zend_Json_Expr(sprintf('function(){%s}', $event));
        }
        $this->setJQueryParam(self::PARAM_BUTTONS, $buttons);
        return $this;
    }

    /**
     * Dodaje do wskazanego elementu kod otwarcia okna dialogowego 
     * przy podanym zdarzeniu
     * 
     * @param \ZendY\Form\Element\Widget|null $element
     * @param string $event
     * @return \ZendY\Form\Container\Dialog
     */
    public function addOpener($element, $event = \ZendY\JQuery::EVENT_CLICK) {
        if ($element instanceof Element\Widget) {
            $element->setOnEvent($event, $this->getJQueryMethod(self::PARAM_METHOD_OPEN));
        } else {
            $js = sprintf('%s("%s").on("%s",%s);'
                    , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                    , $element
                    , $event
                    , \ZendY\JQuery::createJQueryEventObject($this->getJQueryMethod(self::PARAM_METHOD_OPEN))
            );
            $this->getView()->jQuery()->addOnLoad($js);
        }
        return $this;
    }

    /**
     * Dodaje do wskazanych elementów kod otwarcia okna dialogowego 
     * przy podanych zdarzeniach
     * 
     * @param array $openers
     * @return \ZendY\Form\Container\Dialog
     */
    public function setOpeners(array $openers) {
        foreach ($openers as $opener) {
            if (is_array($opener)) {
                $this->addOpener($opener['element'], $opener['event']);
            } else {
                $this->addOpener($opener);
            }
        }
        return $this;
    }

    /**
     * Dodaje do wskazanego elementu kod zamknięcia okna dialogowego 
     * przy podanym zdarzeniu
     * 
     * @param \ZendY\Form\Element\Widget|null $element
     * @param string $event
     * @return \ZendY\Form\Container\Dialog
     */
    public function addCloser($element, $event = \ZendY\JQuery::EVENT_CLICK) {
        if ($element instanceof Element\Widget)
            $element->setOnEvent($event, $this->getJQueryMethod(self::PARAM_METHOD_CLOSE));
        return $this;
    }

}
