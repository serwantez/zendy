<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element;

require_once "ZendX/JQuery/Form/Element/UiWidget.php";

use ZendY\Css;
use ZendY\Exception;
use ZendY\JQuery;

/**
 * Klasa bazowa dla kontrolek
 *
 * @author Piotr Zając
 */
abstract class Widget extends \ZendX_JQuery_Form_Element_UiWidget {

    use \ZendY\Form\CssTrait;

    /**
     * Parametry i metody jQuery
     */

    const PARAM_CREATE = 'create';
    const PARAM_TOOLTIP = 'tooltip';
    const PARAM_FOCUS = 'focus';

    /**
     * Strony modelu pudełkowego obiektów
     */
    const SIDE_TOP = 'top';
    const SIDE_RIGHT = 'right';
    const SIDE_BOTTOM = 'bottom';
    const SIDE_LEFT = 'left';

    /**
     * Tablica nazw zdarzeń
     * 
     * @var array
     */
    protected $_events = array();

    /**
     * Etykieta dolna
     * 
     * @var string
     */
    protected $_subLabel = null;

    /**
     * Opcje dekoratora etykiety
     * 
     * @var array
     */
    protected $_labelOptions = array();

    /**
     * Parametry przekazywane do przeglądarki
     * 
     * @var array
     */
    protected $_frontEditParams = array();

    /**
     * Konstruktor uzupełniony o funkcję automatycznego dodawania nazwy kontrolki
     * 
     * @param string|null $spec
     * @param type|null $options 
     * @return void
     */
    public function __construct($spec = null, $options = null) {
        $this->addPrefixPath('ZendY\Form\Decorator', 'ZendY/Form/Decorator', \Zend_Form::DECORATOR);
        $this::$count++;
        if (!isset($spec)) {
            $spec = get_class($this) . '_' . $this::$count;
        }
        $this->_setDefaults();
        parent::__construct($spec, $options);
        $this->_prepareEventParams();
    }

    /**
     * Funkcja definiująca wartości domyślne właściwości 
     * - uzupełniana w klasach potomnych
     */
    protected function _setDefaults() {
        $this->_labelOptions = array(
            'escape' => false,
            'requiredSuffix' => '*',
            'class' => 'field-label'
        );
    }

    /**
     * Usuwa wybrany parametr jquery
     * 
     * @param string $param
     * @return \ZendY\Form\Element\Widget
     */
    public function removeJQueryParam($param) {
        if (array_key_exists($param, $this->jQueryParams))
            unset($this->jQueryParams[$param]);
        return $this;
    }

    /**
     * Ustawia parametr jQuery
     *
     * @param  string $key
     * @param  string $value
     * @return \ZendY\Form\Element\Widget
     */
    public function setJQueryParam($key, $value) {
        $key = (string) $key;
        if (isset($value)) {
            if (array_key_exists($key, $this->jQueryParams) && is_array($this->jQueryParams[$key])) {
                $this->jQueryParams[$key][] = $value;
            }
            else
                $this->jQueryParams[$key] = $value;
        } else {
            unset($this->jQueryParams[$key]);
        }
        return $this;
    }

    /**
     * Tworzy zmienne tablicowe z parametrów zdarzeń
     * 
     * @return \ZendY\Form\Element\Widget
     */
    protected function _prepareEventParams() {
        //parametry przechowujące kody zdarzeń muszą być tablicami
        foreach ($this->_events as $event) {
            $this->jQueryParams[$event] = array();
        }
        return $this;
    }

    /**
     * Zamienia zmienne tablicowe zdarzeń na zmienne łańcuchowe
     * 
     * @return \ZendY\Form\Element\Widget
     */
    protected function _prepareRenderEventParams() {
        foreach ($this->_events as $event) {
            if (count($this->jQueryParams[$event]) > 0) {
                $operations = implode(PHP_EOL, $this->jQueryParams[$event]);
                $this->jQueryParams[$event] = JQuery::createJQueryEventObject(
                                $operations
                );
            } else {
                unset($this->jQueryParams[$event]);
            }
        }
        return $this;
    }

    /**
     * Ustawia parametr przekazywany do przeglądarki
     * 
     * @param string $paramName
     * @param string $paramValue
     * @return \ZendY\Form\Element\Widget
     */
    public function setFrontEditParam($paramName, $paramValue) {
        $this->_frontEditParams[$paramName] = $paramValue;
        return $this;
    }

    /**
     * Zwraca tablicę parametrów edycyjnych przekazywanych do przeglądarki
     * 
     * @return array
     */
    public function getFrontEditParams() {
        $this->setFrontEditParam('attribs', $this->getAttribs());
        return $this->_frontEditParams;
    }

    /**
     * Ustawia identyfikator obiektu
     * 
     * @param string $id
     * @return \ZendY\Form\Element\Widget
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * Ustawia atrybut tytuł
     * 
     * @param string $title
     * @return \ZendY\Form\Element\Widget
     */
    public function setTitle($title) {
        $this->setAttrib('title', $title);
        return $this;
    }

    /**
     * Zwraca atrybut tytuł
     * 
     * @return string
     */
    public function getTitle() {
        return $this->getAttrib('title');
    }

    /**
     * Ustawia parametry podpowiedzi w tzw. dymku
     * 
     * @param array|null|bool $params
     * @return \ZendY\Form\Element\Widget
     */
    public function setTooltip($params = array()) {
        if ($params !== false) {
            if (is_array($params))
                $this->setJQueryParam(self::PARAM_TOOLTIP, $params);
        } else {
            $this->removeJQueryParam(self::PARAM_TOOLTIP);
        }
        return $this;
    }

    /**
     * Zwraca parametry podpowiedzi w tzw. dymku
     * 
     * @return array|null|bool
     */
    public function getTooltip() {
        return $this->getJQueryParam(self::PARAM_TOOLTIP);
    }

    /**
     * Ustawia etykietę
     * 
     * @param string $label
     * @return \ZendY\Form\Element\Widget
     */
    public function setLabel($label, $width = null) {
        if (is_array($label)) {
            if (isset($label['width'])) {
                $width = $label['width'];
            }
            $label = $label['text'];
        }
        if (isset($width)) {
            if (!is_array($width))
                $width = array(
                    'value' => $width,
                    'unit' => 'px'
                );
            $this->_labelOptions['style'] = 'width: ' . \ZendY\View\Helper\Widget::implodeArrayProperty($width) . ';';
        }
        $this->loadDecorators();
        parent::setLabel($label);
        return $this;
    }

    /**
     * Czy jest ustawiona etykieta
     * 
     * @return bool 
     */
    public function isLabel() {
        return (isset($this->_label));
    }

    /**
     * Ustawia etykietę dolną
     * 
     * @todo zrobić osobny pomocnik widoku dla znacznika label
     * @return \ZendY\Form\Element\Widget
     */
    public function setSubLabel($subLabel) {
        $this->_subLabel = $subLabel;
        $this->_label .= '<br /><small>' . $this->_subLabel . '</small>';
        return $this;
    }

    /**
     * Sumuje rozmiary tablicowe z wydzieloną wartością i jednostką
     * 
     * @param array $sizes
     * @return array
     */
    static public function sumSizes(array $sizes) {
        $result = 0;
        $actualUnit = NULL;
        foreach ($sizes as $size) {
            if (is_array($size)) {
                if (!isset($actualUnit)) {
                    $actualUnit = $size['unit'];
                }
                if ($size['unit'] == $actualUnit) {
                    $result += $size['value'];
                } else {
                    throw new Exception(sprintf('Invalid unit (%s) of size', $size['unit']));
                }
                $actualUnit = $size['unit'];
            }
        }
        return array('value' => $result, 'unit' => $actualUnit);
    }

    /**
     * Ustawia fokus na kontrolce
     * 
     * @return \ZendY\Form\Element\Widget
     */
    public function setFocus($focus = null) {
        if ($focus !== false) {
            $this->setJQueryParam(self::PARAM_FOCUS, true);
        } else {
            $this->removeJQueryParam(self::PARAM_FOCUS);
        }
        return $this;
    }

    /**
     * Zwraca informację o fokusie na kontrolce
     * 
     * @return null|bool
     */
    public function getFocus() {
        return $this->getJQueryParam(self::PARAM_FOCUS);
    }

    /**
     * Wywołuje na obiekcie jquery metodę obsługującą podane zdarzenie
     * 
     * @link http://api.jquery.com/on/
     * @param string $event
     * @param string $operations
     * @param string|null $selector
     * @param array|null $data
     * @return \ZendY\Form\Element\Widget
     */
    public function setOnEvent($event, $operations = '', $selector = null, $data = array()) {
        $js = sprintf('%s("#%s").on("%s","%s",%s,%s);'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $this->_name
                , $event
                , $selector
                , \ZendX_JQuery::encodeJson($data)
                , JQuery::createJQueryEventObject($operations)
        );
        $this->getView()->jQuery()->addOnLoad($js);
        return $this;
    }

    /**
     * Ustawia kod operacji dla wystąpienia zdarzenia przewidzianego w kontrolce
     * 
     * @param string $event
     * @param string $operation
     * @return \ZendY\Form\Element\Widget
     */
    public function setOnEventParam($event, $operations) {
        $this->setJQueryParam($event, JQuery::createJQueryEventObject($operations));
        return $this;
    }

    /**
     * Ładuje domyślne dekoratory
     *
     * @return \ZendY\Form\Element\Widget
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->setDecorators(array(
                array('UiWidgetElement'),
                array('Errors', array(
                        'tag' => 'ul',
                        'class' => Css::STATE_ERROR . ' ' . Css::CORNER_ALL . ' ' . Css::INVISIBLE
                )),
                array('Description', array('tag' => 'span', 'class' => 'field-description')),
                array(array('Section' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field-container'))
            ));
        }
        return $this;
    }

    /**
     * Ładuje dekoratory
     * 
     * @return \ZendY\Form\Element\Widget
     */
    public function loadDecorators() {
        $this->setDecorators(array(
            array('UiWidgetElement'),
            array('Errors', array(
                    'tag' => 'ul',
                    'class' => Css::STATE_ERROR . ' ' . Css::CORNER_ALL . ' ' . Css::INVISIBLE
            )),
            array('Description', array('tag' => 'span', 'class' => 'field-description')),
            array('Label', $this->_labelOptions),
            array(array('Section' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field-container'))
        ));
        return $this;
    }

    /**
     * Tworzy pasek Tooltip na podstawie komunikatów o błędach 
     * 
     * @return \ZendY\Form\Element\Widget
     */
    protected function _setErrorsTooltip() {
        $this->addClass(Css::STATE_ERROR);
        $errors = \ZendY\JQuery::encodeJson($this->getMessages());
        $params = array(
            'items' => '.' . Css::STATE_ERROR,
            'content' => new \Zend_Json_Expr(sprintf('function() {
                    var errors = %s;
                    var errorsjoined = "";
                    for (var i in errors) {
                    errorsjoined += errors[i]+"<br />";
                    }
                    return errorsjoined;
                    }', $errors)),
            'tooltipClass' => Css::STATE_ERROR,
            'position' => array('my' => 'left+10 center', 'at' => 'right center'));
        $this->setTooltip($params);
        return $this;
    }

    /**
     * Validate element value
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $valid = parent::isValid($value, $context);
        if (!$valid) {
            $this->_setErrorsTooltip();
        }
        return $valid;
    }

    /**
     * Add an error message and mark element as failed validation
     *
     * @param  string $message
     * @return \ZendY\Form\Element\Widget
     */
    public function addError($message) {
        parent::addError($message);
        $this->_setErrorsTooltip();
    }

    /**
     * Dodaje walidator ograniczający liczbę znaków
     * 
     * @param int|null $min
     * @param int|null $max
     * @return \ZendY\Form\Element\Widget
     */
    public function setStringLength($min = 1, $max = 64) {
        $this->addValidator(new \Zend_Validate_StringLength(array('min' => $min, 'max' => $max)));
        return $this;
    }

    /**
     * Włącza/wyłącza aktywność kontrolki
     * 
     * @param bool $disabled
     * @return \ZendY\Form\Element\Widget
     */
    public function setDisabled($disabled) {
        if ($disabled)
            $this->setAttrib('disabled', 'disabled');
        else {
            $this->setAttrib('disabled', NULL);
        }
        return $this;
    }

    /**
     * Zwraca informację, czy kontrolka jest wyłączona
     * 
     * @return bool 
     */
    public function getDisabled() {
        if ($this->getAttrib('disabled') == 'disabled')
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Włącza/wyłącza tryb tylko do odczytu dla kontrolki
     * 
     * @param bool $readOnly
     * @return \ZendY\Form\Element\Widget
     */
    public function setReadOnly($readOnly) {
        if ($readOnly)
            $this->setAttrib('readonly', 'readonly');
        else {
            /* $attr = $this->getAttribs();
              unset($attr['readonly']); */
            $this->setAttrib('readonly', NULL);
        }
        return $this;
    }

    /**
     * Zwraca informację czy kontrolka jest tylko do odczytu
     * 
     * @return bool 
     */
    public function getReadOnly() {
        if ($this->getAttrib('readonly') == 'readonly')
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Renderuje element
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        \ZendY\Msg::add(sprintf('Widget %s ->%s', $this->getId(), __FUNCTION__));
        $this->_prepareRenderEventParams();
        return parent::render($view);
    }

}
