<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

use ZendY\Msg;
use ZendY\Exception;
use ZendY\Db\DataSource;
use ZendY\Db\Form\Element;
use ZendY\Form\Container\Dialog;
use ZendY\Css;

/**
 * Formularz bazodanowy
 *
 * @author Piotr Zając
 */
class Form extends \ZendY\Form {

    /**
     * Źródła rekordu
     * 
     * @var array
     */
    protected $_dataSources = array();

    /**
     * Listy danych
     * 
     * @var array
     */
    protected $_listSources = array();

    /**
     * Źródła danych, z których korzysta formularz
     * 
     * @var null|array
     */
    protected $_sources = null;

    /**
     * Czy obiekt ma mieć okno informacyjne
     * 
     * @var bool
     */
    protected $_dialog = true;

    /**
     * Konstruktor
     * @param array|Zend_Config|null $options
     * @param array|null $dataSources
     * @return void
     */
    public function __construct($options = null, $sources = null) {
        if (isset($sources) && is_array($sources)) {
            $this->_sources = $sources;
        }
        parent::__construct($options);
    }

    /**
     * Zaznacza, że formularz poddany jest serializacji
     * 
     * @return array
     */
    public function __sleep() {
        Msg::add('Formularz ' . $this->getId() . '->' . __FUNCTION__);
        return array_keys(get_object_vars($this));
    }

    /**
     * Zaznacza, że formularz poddany jest deserializacji
     * 
     * @return void
     */
    public function __wakeup() {
        Msg::add('Formularz ' . $this->getId() . '->' . __FUNCTION__);
    }

    /**
     * Zwraca podane źródło danych
     * 
     * @param string $id
     * @return null|\ZendY\Db\DataSource
     */
    public function getSource($id) {
        if (isset($this->_sources[$id])) {
            return $this->_sources[$id];
        } elseif (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            return ActionManager::getInstance()->getDataSource($id);
        } else {
            return null;
        }
    }

    /**
     * Dodaje pojedyncze źródło danych do formularza
     * 
     * @param \ZendY\Db\DataSource $dataSource
     * @return \ZendY\Db\Form
     */
    protected function _addDataSource(DataSource $dataSource) {
        $dataSource->setForm($this);
        $this->_dataSources[$dataSource->getId()] = $dataSource;

        return $this;
    }

    /**
     * Tworzy dynamicznie formularz
     * 
     * @param string $className
     * @param array|null $options
     * @return \ZendY\Db\Form|false
     */
    static public function getInstance($className, $options = null) {
        if (class_exists($className)) {
            return new $className($options);
        }
        else
            return false;
    }

    /**
     * Dodaje źródła danych do formularza
     * 
     * @param array $dataSources
     * @return \ZendY\Db\Form
     */
    protected function _addDataSources(array $dataSources) {
        foreach ($dataSources as $dataSource) {
            $this->_addDataSource($dataSource);
        }
        return $this;
    }

    /**
     * Zwraca źródła danych
     * 
     * @return array
     */
    public function getDataSources() {
        return $this->_dataSources;
    }

    /**
     * Zwraca identyfikatory źródeł danych
     * 
     * @return array
     */
    public function getDataSourcesId() {
        $result = array();
        foreach ($this->_dataSources as $dataSource) {
            $result[] = $dataSource->getId();
        }
        return $result;
    }

    /**
     * Dodaje pojedynczą listę danych do formularza
     * 
     * @param \ZendY\Db\DataSource $listSource
     * @return \ZendY\Db\Form
     */
    protected function _addListSource(DataSource $listSource) {
        $listSource->setForm($this);
        $this->_listSources[$listSource->getId()] = $listSource;
        return $this;
    }

    /**
     * Dodaje listy danych do formularza
     * 
     * @param array $listSources
     * @return \ZendY\Db\Form
     */
    protected function _addListSources(array $listSources) {
        foreach ($listSources as $listSource) {
            $this->_addListSource($listSource);
        }
        return $this;
    }

    /**
     * Zwraca listy danych
     * 
     * @return array
     */
    public function getListSources() {
        return $this->_listSources;
    }

    /**
     * Zwraca identyfikatory list danych
     * 
     * @return array
     */
    public function getListSourcesId() {
        $result = array();
        foreach ($this->_listSources as $listSource) {
            $result[] = $listSource->getId();
        }
        return $result;
    }

    /**
     * Dodaje element do formularza
     * 
     * @param  string|\ZendY\Form\Element\Widget $element
     * @param  string $name
     * @param  array|\Zend_Config $options
     * @return \ZendY\Db\Form
     */
    public function addElement($element, $name = null, $options = null) {
//jeśli element jest dowolną kontrolką bazodanową
        if ($element instanceof Element\CellInterface) {
            $dataSource = $element->getDataSource();
//jeśli kontrolka ma ustawione źródło danych i źródła tego nie ma jeszcze w spisie (w tablicy źródeł danych)
            if (isset($dataSource) && !array_key_exists($dataSource->getId(), $this->_dataSources))
                $this->_addDataSource($dataSource);
//jeśli element jest listą bazodanową
            if ($element instanceof Element\ColumnInterface && !$element->getStaticRender()) {
                $listSource = $element->getListSource();
//jeśli źródła listy nie ma jeszcze w spisie (w tablicy źródeł listy)
                if (isset($listSource) && !array_key_exists($listSource->getId(), $this->_listSources))
                    $this->_addListSource($listSource);
            }
        }
        parent::addElement($element, $name, $options);
        return $this;
    }

    /**
     * Waliduje formularz (wraz z podformularzami) dla podanego źródła danych
     * 
     * @param array $data
     * @param string $dataSourceId
     * @return bool
     * @throws Exception
     */
    public function isValidDataSource(array $data, $dataSourceId) {
        if (!is_array($data)) {
            throw new Exception(__METHOD__ . ' expects an array');
        }
        $translator = $this->getTranslator();
        $valid = true;
        $eBelongTo = null;

        if ($this->isArray()) {
            $eBelongTo = $this->getElementsBelongTo();
            $data = $this->_dissolveArrayValue($data, $eBelongTo);
        }
        $context = $data;
        foreach ($this->getElements() as $key => $element) {
            if ($element instanceof Element\CellInterface
                    && (!$element instanceof Element\PresentationInterface)) {
//echo(get_class($element).' ');
                $ds = $element->getDataSource();
                if (isset($ds) && $ds->getId() == $dataSourceId) {
                    if (null !== $translator && $this->hasTranslator()
                            && !$element->hasTranslator()) {
                        $element->setTranslator($translator);
                    }
                    $check = $data;
                    if (($belongsTo = $element->getBelongsTo()) !== $eBelongTo) {
                        $check = $this->_dissolveArrayValue($data, $belongsTo);
                    }
                    if (!isset($check[$key])) {
                        $valid = $element->isValid(null, $context) && $valid;
                    } else {
                        $valid = $element->isValid($check[$key], $context) && $valid;
                        $data = $this->_dissolveArrayUnsetKey($data, $belongsTo, $key);
                    }
                }
            }
        }
        foreach ($this->getSubForms() as $key => $form) {
            if (null !== $translator && $this->hasTranslator()
                    && !$form->hasTranslator()) {
                $form->setTranslator($translator);
            }
            if (isset($data[$key]) && !$form->isArray()) {
                $valid = $form->isValidDataSource($data[$key], $dataSourceId) && $valid;
            } else {
                $valid = $form->isValidDataSource($data, $dataSourceId) && $valid;
            }
        }

        $this->_errorsExist = !$valid;

// If manually flagged as an error, return invalid status
        if ($this->_errorsForced) {
            return false;
        }

        return $valid;
    }

    /**
     * Renderuje zawartość okna dialogowego
     * 
     * @return string
     */
    protected function _renderDialogContent() {
        return sprintf('<img src="%s/library/components/dialog/ajax-loader.gif" /> <span>%s</span>'
                        , $this->getView()->host
                        , $this->getView()->translate(Msg::MSG_DATA_LOADING)
        );
    }

    /**
     * Generuje kod formularza
     * 
     * @param \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        Msg::add('Formularz db ' . $this->getId() . '->' . __FUNCTION__);
        $result = '';
        $js = array();

        $result .= parent::render($view);

        if (!$this->_isSubForm) {
            /**
             * okno dialogowe
             */
            if ($this->_dialog) {
                $params = array(
                    Dialog::PARAM_AUTOOPEN => false,
                    Dialog::PARAM_CLOSEONESCAPE => false,
                    Dialog::PARAM_DRAGGABLE => false,
                    Dialog::PARAM_MODAL => true,
                    Dialog::PARAM_RESIZABLE => false,
                    Dialog::PARAM_WIDTH => 180,
                    Dialog::PARAM_MIN_HEIGHT => 40,
                    Dialog::PARAM_MAX_HEIGHT => 40,
                    Dialog::PARAM_EVENT_CREATE => new \Zend_Json_Expr(sprintf('function(event, ui) { $(this).parent().children(".%s").hide(); }'
                                    , Css::DIALOG_TITLEBAR))
                );
                $result .= $this->getView()->dialogContainer(
                        $this->getId() . '_dialog'
                        , $this->_renderDialogContent()
                        , $params
                        , array('class' => Css::DIALOG_LOADING)
                );
            }

            $formClass = \ZendY\JQuery::encodeJson(explode('\\', get_class($this)));

//otwarcie formularza po stronie przeglądarki
            $js[] = sprintf('df["%s"] = new dataForm("%s",%s,"%s","%s");'
                    , $this->getId()
                    , $this->getId()
                    , $formClass
                    , DataSource::$controller . DataSource::$dataAction
                    , $this->getView()->translate(Msg::MSG_FORM_VALIDATION_ERRORS));
            if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
                $result .= '<script>' . implode("\n", $js) . '</script>';
            } else {
                $this->getView()->jQuery()->addOnLoad(implode("\n", $js));
            }

            foreach ($this->_listSources as $listSource) {
                $result .= $listSource->render();
            }

            foreach ($this->_dataSources as $dataSource) {
                $result .= $dataSource->render();
            }

//zapisywanie źródeł
            Msg::add('Zaczynam zapisywanie źródeł list');
            foreach ($this->_listSources as $listSource) {
                $listSource->saveObject();
            }

            Msg::add('Zaczynam zapisywanie źródeł danych');
            foreach ($this->_dataSources as $dataSource) {
                $dataSource->saveObject();
            }

            $js = sprintf('df["%s"].open();', $this->getId());
            if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
                $result .= '<script>' . $js . '</script>';
            } else {
                $this->getView()->jQuery()
                        ->addJavascriptFile($this->getView()->host . '/library/components/form/ajax-validator.js')
                        ->addJavascriptFile($this->getView()->host . '/library/components/form/dataform.js')
                        ->addOnLoad($js);
            }
        }
        return $result;
    }

    /**
     * Dodaje podformularz
     * 
     * @param \Zend_Form $form
     * @param string $name
     * @param int|null $order
     * @return \ZendY\Db\Form
     */
    public function addSubForm(\Zend_Form $form, $name, $order = null) {
        if ($form instanceof Form) {
            $this->_addDataSources($form->getDataSources());
            $this->_addListSources($form->getListSources());
        }
        parent::addSubForm($form, $name, $order);
        return $this;
    }

}

