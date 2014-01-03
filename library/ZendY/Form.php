<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

require_once 'ZendX/JQuery/Form.php';
require_once 'Zend/Layout.php';

use ZendY\Css;
use ZendY\Exception;
use ZendY\Form\Container\Base as Container;
use ZendY\Form\Element;

/**
 * Formularz standardowy
 *
 * @author Piotr Zając
 */
class Form extends \ZendX_JQuery_Form {

    use Form\CssTrait;

    /**
     * Status walidacji ajaxowej: 0-wyłączona, 1-włączona
     * 
     * @var bool 
     */
    protected $_ajaxValidator = false;

    /**
     * Czy formularz jest podformularzem
     * 
     * @var bool 
     */
    protected $_isSubForm = false;

    /**
     * Konstruktor
     * 
     * @param array|Zend_Config|null $options 
     * @return void
     */
    public function __construct($options = null) {
        $this->addPrefixPath('ZendY\Form\Decorator', 'ZendY/Form/Decorator', \Zend_Form::DECORATOR);
        $this->setAjaxValidator();
        $this->setMethod(\Zend_Form::METHOD_POST);
        $this->addClasses(array(
            Css::WIDGET,
            Css::FORM));
        parent::__construct($options);
    }

    /**
     * Włącza/wyłącza walidację ajaxową
     * 
     * @param bool $ajaxValidator
     * @return \ZendY\Form
     */
    public function setAjaxValidator($ajaxValidator = true) {
        $this->_ajaxValidator = $ajaxValidator;
        return $this;
    }

    /**
     * Zwraca informację o włączeniu/wyłączeniu walidacji ajaxowej
     * 
     * @return bool 
     */
    public function getAjaxValidator() {
        return $this->_ajaxValidator;
    }

    /**
     * Porządkuje tablicę wyników walidacji formularza
     * 
     * @param \ZendY\Form $f
     * @param array $messages
     * @return array
     */
    static public function prepareFormMessages(\ZendY\Form $f, array $messages) {
        $data = array();
        //sprawdzenie czy formularz ma podformularze
        $subforms = $f->getSubForms();
        if ($subforms) {
            foreach ($subforms as $key => $form) {
                if (array_key_exists($key, $messages)) {
                    //czy komunikaty znajdują się w podformularzu
                    $data = array_merge($data, self::prepareFormMessages($form, $messages[$key]));
                    //usuwa komunikaty zawarte w podformularzu
                    unset($messages[$key]);
                }
            }
        }
        //komunikaty kontrolek nie należacych do podformularza
        if (count($messages)) {
            //dodaje komunikaty do wyniku
            $data = array_merge($data, $messages);
        }
        return $data;
    }

    /**
     * Ładuje domyślne dekoratory
     * 
     * @return \ZendY\Form
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Description')
                    ->addDecorator('FormElements')
                    ->addDecorator('HtmlTag', array('tag' => 'dl'))
                    ->addDecorator('Form');
        }
        return $this;
    }

    /**
     * Ustawia czy formularz jest podformularzem
     * 
     * @param bool $flag
     * @return \ZendY\Form
     */
    public function setIsSubForm($flag) {
        $this->_isSubForm = (bool) $flag;
        return $this;
    }

    /**
     * Zwraca informację, czy formularz jest podforularzem
     * 
     * @return bool
     */
    public function getIsSubForm() {
        return $this->_isSubForm;
    }

    /**
     * Zwraca wszystkie elementy formularza wraz z elementami podformularzy
     * 
     * @param \ZendY\Form $container
     * @return array
     */
    public function getAllElements(Form $container = null, $class = null) {
        if (!isset($container))
            $container = $this;
        $result = array();
        $elements = $this->getElements();
        if (isset($class)) {
            foreach ($elements as $element) {
                if ($element instanceof $class) {
                    $result[] = $element;
                }
            }
        } else {
            $result = $elements;
        }
        foreach ($this->getSubForms() as $subform) {
            $result = array_merge($result, $subform->getAllElements());
        }

        return $result;
    }

    /**
     * Dodaje podformularz
     * 
     * @param \Zend_Form $form
     * @param string $name
     * @param int|null $order 
     * @return \ZendY\Form
     */
    public function addSubForm(\Zend_Form $form, $name, $order = null) {
        if ($form instanceof Form) {
            $form->removeDecorator('Form');
            $form->setIsSubForm(true);
            parent::addSubForm($form, $name, $order);
        } else {
            throw new Exception('Subform must be ZendY\Form instance');
        }
        return $this;
    }

    /**
     * Dodaje kontener
     * 
     * @param \ZendY_Form_Container_Custom $container
     * @param string $name
     * @return \ZendY\Form
     */
    public function addContainer(Container $container, $name = null) {
        if (!isset($name)) {
            $name = $container->getId();
        }

        /**
         * Pozycjonowanie
         */
        $align = $container->getAlign();
        if ($align) {
            // pobranie wszystkich dodanych już kontenerów
            $existingContainers = $this->getSubForms();

            foreach ($existingContainers as $key => $existingContainer) {

                switch ($align) {
                    case Css::ALIGN_LEFT:
                        $existingContainers[$key]->addAlignMargin(Element\Widget::SIDE_LEFT, $container->getWidth());
                        $existingContainers[$key]->refreshDecorators();
                        break;
                    case Css::ALIGN_RIGHT:
                        $existingContainers[$key]->addAlignMargin(Element\Widget::SIDE_RIGHT, $container->getWidth());
                        $existingContainers[$key]->refreshDecorators();
                        break;
                    case Css::ALIGN_TOP:
                        $existingContainers[$key]->addAlignMargin(Element\Widget::SIDE_TOP, $container->getHeight());
                        $existingContainers[$key]->refreshDecorators();
                        break;
                    case Css::ALIGN_BOTTOM:
                        $existingContainers[$key]->addAlignMargin(Element\Widget::SIDE_BOTTOM, $container->getHeight());
                        $existingContainers[$key]->refreshDecorators();
                        break;
                    case Css::ALIGN_CLIENT:
                        $existingContainerAlign = $existingContainers[$key]->getAlign();
                        switch ($existingContainerAlign) {
                            case Css::ALIGN_LEFT:
                                $container->addAlignMargin(Element\Widget::SIDE_LEFT, $existingContainers[$key]->getWidth());
                                break;
                            case Css::ALIGN_RIGHT:
                                $container->addAlignMargin(Element\Widget::SIDE_RIGHT, $existingContainers[$key]->getWidth());
                                break;
                            case Css::ALIGN_TOP:
                                $container->addAlignMargin(Element\Widget::SIDE_TOP, $existingContainers[$key]->getHeight());
                                break;
                            case Css::ALIGN_BOTTOM:
                                $container->addAlignMargin(Element\Widget::SIDE_BOTTOM, $existingContainers[$key]->getHeight());
                                break;
                            case Css::ALIGN_CLIENT:
                                //formularze nałożone na siebie
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        break;
                }
            }

            $this->setSubForms($existingContainers);
        }

        if ($container instanceof Container) {
            $container->onContain();
        }

        $this->addSubForm($container, $name);
        return $this;
    }

    /**
     * Dodaje do formularza wiele kontenerów na raz
     * 
     * @param array $containers
     * @return \ZendY\Form
     */
    public function addContainers(array $containers) {
        foreach ($containers as $container) {
            $this->addContainer($container);
        }
        return $this;
    }

    /**
     * Wyszukuje i zwraca element formularza (z uwzględnieniem podformularzy)
     * 
     * @param string $name
     * @return \Zend_Form_Element|null
     */
    public function getNestedElement($name) {
        if ($element = $this->getElement($name)) {
            return $element;
        }
        foreach ($this->getSubForms() as $subForm) {
            if ($element = $subForm->getNestedElement($name)) {
                return $element;
            }
        }
        return null;
    }

    /**
     * Wyszukuje i zwraca wartość elementu formularza (z uwzględnieniem podformularzy)
     *
     * @param  string $name
     * @return mixed
     */
    public function getNestedValue($name) {
        if ($element = $this->getElement($name)) {
            return $element->getValue();
        }

        foreach ($this->getSubForms() as $subForm) {
            if ($element = $subForm->getNestedElement($name)) {
                return $element->getValue();
            }
        }
        return null;
    }

    /**
     * Renderuje formularz
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        Msg::add('Formularz ' . $this->getId() . '->' . __FUNCTION__);
        //Walidacja ajaxowa
        if ($this->_ajaxValidator && !$this->_isSubForm) {
            $formClass = \ZendX_JQuery::encodeJson(explode('\\', get_class($this)));
            $js = sprintf('%s("%s").blur(function(){validate(%s, %s(this).attr("name"), "%s")});'
                    , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                    , "input, select, textarea"
                    , $formClass
                    , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                    , \ZendY\Db\DataSource::$controller . 'validateform'
            );
            $this->getView()->jQuery()->addOnLoad($js);
        }
        return parent::render($view);
    }

}