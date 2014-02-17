<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

use ZendY\Msg,
    ZendY\Exception,
    ZendY\Component,
    ZendY\Db\DataSet\Base as DataSet,
    ZendY\Db\DataSet\Editable,
    ZendY\Db\DataSet\TableInterface,
    ZendY\Db\Form as DbForm,
    ZendY\Form\Container\Dialog,
    ZendY\Db\Form\Element,
    ZendY\Css;

/**
 * Źródło danych
 *
 * @author Piotr Zając
 */
class DataSource extends Component {
    /**
     * Stany obiektu
     */

    const STATE_NULL = 0;
    const STATE_CREATED = 1;
    const STATE_SERIALIZED = 2;
    const STATE_UNSERIALIZED = 3;

    /**
     * Właściwości komponentu
     */
    const PROPERTY_DATASET = 'dataSet';
    const PROPERTY_DIALOG = 'dialog';

    /**
     * Zbiór danych
     * 
     * @var \ZendY\Db\DataSet\Base 
     */
    protected $_dataSet;

    /**
     * Czy źródło danych zostało wyrenderowane
     * 
     * @var bool
     */
    protected $_rendered = false;

    /**
     * Stan obiektu
     * 
     * @var int 
     */
    protected $_state = self::STATE_NULL;

    /**
     * Kontrolki edycyjne powiązane z tym źródłem danych
     * 
     * @var array
     */
    protected $_editControls = array();

    /**
     * Kontrolki nawigacyjne powiązane z tym źródłem danych
     * 
     * @var array
     */
    protected $_naviControls = array();

    /**
     * Kontrolki filtrujące powiązane z tym źródłem danych
     * 
     * @var array
     */
    protected $_filterControls = array();

    /**
     * Kontrolki prezentacyjne powiązane z tym źródłem danych
     * 
     * @var array
     */
    protected $_presentationControls = array();

    /**
     * Kontrolki stanu powiązane z tym źródłem danych
     * 
     * @var array
     */
    protected $_stateControls = array();

    /**
     * Kontrolki raportu powiązane z tym źródłem danych
     * 
     * @var array
     */
    protected $_reportControls = array();

    /**
     * Adres kontrolera bazodanowego
     * 
     * @var string
     */
    static public $controller = '/data/';

    /**
     * Akcja zwracająca dane w kontrolerze bazodanowym
     * 
     * @var string
     */
    static public $dataAction = 'data';

    /**
     * Akcja zwracająca podpowiedzi w kontrolce (Db)Autocomplete i pochodnych
     * 
     * @var string
     */
    static public $autoCompleteAction = 'autocomplete';

    /**
     * Akcja zwracająca obraz w kontrolce (Db)Image i pochodnych
     * 
     * @var string
     */
    static public $imageAction = 'image';

    /**
     * Akcja do wysyłania obrazu
     * 
     * @var string
     */
    static public $uploadAction = 'upload';

    /**
     * Formularz - właściciel
     * 
     * @var \ZendY\Db\Form
     */
    protected $_form;

    /**
     * Czy obiekt ma mieć okno informacyjne
     * 
     * @var bool
     */
    protected $_dialog = true;

    /**
     * Tablica właściwości komponentu
     * 
     * @var array
     */
    protected $_properties = array(
        self::PROPERTY_DATASET,
        self::PROPERTY_DIALOG,
        self::PROPERTY_NAME,
    );

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this->_state = self::STATE_CREATED;
    }

    /**
     * Ustawia zbiór danych
     * 
     * @param \ZendY\Db\DataSet\Base $dataSet
     * @return \ZendY\Db\DataSource
     */
    public function setDataSet(DataSet $dataSet) {
        $this->_dataSet = $dataSet;
        if (!isset($this->_name)) {
            $this->_name = $this->_dataSet->getName() . 'DataSource';
        }
        return $this;
    }

    /**
     * Zwraca zbiór danych
     * 
     * @return \ZendY\Db\DataSet\Base
     */
    public function getDataSet() {
        return $this->_dataSet;
    }

    /**
     * Ustawia formularz
     * 
     * @param \ZendY\Db\Form $form
     * @return \ZendY\Db\DataSource
     */
    public function setForm(DbForm $form) {
        $this->_form = $form;
        return $this;
    }

    /**
     * Zwraca formularz - właściciela
     * 
     * @return \ZendY\Db\Form
     */
    public function getForm() {
        return $this->_form;
    }

    /**
     * Zwraca identyfikator formularza
     * 
     * @return string
     */
    public function getFormId() {
        return $this->_form->getName();
    }

    /**
     * Ustawia wyświetlanie okna informacyjnego
     * 
     * @param bool|null $dialog
     * @return \ZendY\Db\DataSource
     */
    public function setDialog($dialog = true) {
        $this->_dialog = $dialog;
        return $this;
    }

    /**
     * Zwraca informację o wyświetlaniu okna informacyjnego
     * 
     * @return bool 
     */
    public function getDialog() {
        return $this->_dialog;
    }

    /**
     * Dodaje kontrolkę edycyjną
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource 
     */
    public function addEditControl(\ZendY\Form\Element\Widget &$control) {
        $this->refreshEditControl($control);
        $this->_editControls[$control->getName()] = $control;
        return $this;
    }

    /**
     * Zwraca wszystkie kontrolki edycyjne podłaczone do tego żródła
     * 
     * @return array
     */
    public function getEditControls() {
        return $this->_editControls;
    }

    /**
     * Dostosowuje parametry kontrolki edycyjnej do wymagań zbioru danych
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource
     */
    public function refreshEditControl(\ZendY\Form\Element\Widget &$control) {
        $dataSet = $this->getDataSet();
        $dataField = $control->getDataField();

        if (isset($dataField)) {
            if (($dataSet instanceof TableInterface) &&
                    !($control instanceof Element\Filter\FilterInterface) &&
                    !($control instanceof Element\PresentationInterface)) {
                if ($col = $dataSet->describeField($dataSet->getTableField($dataField))) {
                    //jeśli przypisane pole w tabeli nie może mieć wartości null, dodaje do kontrolki "wymagalność"
                    if (!$col['NULLABLE']) {
                        $control->setRequired(true);
                    }
                    //ograniczenie liczby znaków w kolumnie
                    if ($col['DATA_TYPE'] == 'varchar' and $col['LENGTH']) {
                        $control->addValidator(new \Zend_Validate_StringLength(array('max' => $col['LENGTH'])));
                    }
                }
            }
            if (!$control instanceof Element\PresentationInterface) {
                //zbiór jest w(y)łączony
                if ($dataSet->getState() == DataSet::STATE_OFF) {
                    $control->setDisabled(true);
                } else {
                    $control->setDisabled(false);
                }

                //zbiór jest tylko do odczytu
                if ($dataSet->getReadOnly() || $dataSet->getState() == Editable::STATE_VIEW) {
                    $control->setReadOnly(true);
                } else {
                    $control->setReadOnly(false);
                }
            }
        }
        return $this;
    }

    /**
     * Dodaje kontrolkę nawigacyjną
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource
     */
    public function addNaviControl(\ZendY\Form\Element\Widget &$control) {
        $this->refreshNaviControl($control);
        $this->_naviControls[$control->getName()] = $control;
        return $this;
    }

    /**
     * Zwraca wszystkie kontrolki nawigacyjne podłaczone do tego żródła
     * 
     * @return array
     */
    public function getNaviControls() {
        return $this->_naviControls;
    }

    /**
     * Dostosowuje parametry kontrolki nawigacyjnej do wymagań zbioru danych
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource
     */
    public function refreshNaviControl(\ZendY\Form\Element\Widget &$control) {
        $dataSet = $this->getDataSet();
        //zbiór jest włączony
        if (isset($dataSet) && $dataSet->getState()) {
            $control->setDisabled(false);
        } else {
            $control->setDisabled(true);
        }
        return $this;
    }

    /**
     * Dodaje kontrolkę stanu zbioru
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource
     */
    public function addStateControl(\ZendY\Form\Element\Widget &$control) {
        $this->refreshStateControl($control);
        $this->_stateControls[$control->getName()] = $control;
        return $this;
    }

    /**
     * Zwraca wszystkie kontrolki stanu zbioru podłaczone do tego żródła
     * 
     * @return array
     */
    public function getStateControls() {
        return $this->_stateControls;
    }

    /**
     * Dostosowuje parametry kontrolki stanu do wymagań zbioru danych
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource
     */
    public function refreshStateControl(\ZendY\Form\Element\Widget &$control) {
        $dataSet = $this->getDataSet();
        $control->setAttrib('size', strlen((string) $dataSet->getRecordCount()));
        if (in_array($control->getExpr(), array(
                    Dataset::EXPR_COUNT,
                    Dataset::EXPR_PAGECOUNT
                ))) {
            $control->setReadOnly(TRUE);
        }
        //zbiór jest włączony
        if ($dataSet->getState()) {
            $control->setDisabled(false);
        } else {
            $control->setDisabled(true);
        }
        return $this;
    }

    /**
     * Dodaje kontrolkę filtrującą
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource
     */
    public function addFilterControl(\ZendY\Form\Element\Widget &$control) {
        $this->refreshFilterControl($control);
        $this->_filterControls[$control->getName()] = $control;
        return $this;
    }

    /**
     * Zwraca wszystkie kontrolki filtrujące podłaczone do tego żródła
     * 
     * @return array
     */
    public function getFilterControls() {
        return $this->_filterControls;
    }

    /**
     * Dostosowuje parametry kontrolki filtrującej do wymagań zbioru danych
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource
     */
    public function refreshFilterControl(\ZendY\Form\Element\Widget &$control) {
        $dataSet = $this->getDataSet();
        //zbiór jest włączony
        if ($dataSet->getState()) {
            $control->setDisabled(false);
        } else {
            $control->setDisabled(true);
        }
        return $this;
    }

    /**
     * Dodaje kontrolkę prezentacyjną
     * 
     * @param \ZendY\Form\Element\Widget $control
     * @return \ZendY\Db\DataSource 
     */
    public function addPresentationControl(\ZendY\Form\Element\Widget &$control) {
        $this->_presentationControls[$control->getName()] = $control;
        return $this;
    }

    /**
     * Zwraca wszystkie kontrolki prezentacyjne podłaczone do tego żródła
     * 
     * @return array
     */
    public function getPresentationControls() {
        return $this->_presentationControls;
    }

    /**
     * Dodaje kontrolkę raportu
     * 
     * @param \ZendY\Report\Element $control
     * @return \ZendY\Db\DataSource
     */
    public function addReportControl(\ZendY\Report\Element &$control) {
        $this->_reportControls[$control->getName()] = $control;
        return $this;
    }

    /**
     * Zwraca wszystkie kontrolki raportu podłaczone do tego żródła
     * 
     * @return array
     */
    public function getReportControls() {
        return $this->_reportControls;
    }

    /**
     * Serializacja kontrolek
     * 
     * @return void
     */
    protected function _serializeControls() {
        Msg::add($this->getName() . '->' . __FUNCTION__);
        foreach ($this->_editControls as $key => $control) {
            if (is_object($control)) {
                $this->_editControls[$key] = array('dataField' => $control->getDataField());
                if (method_exists($control, 'formatValue')) {
                    $this->_editControls[$key]['formatValueFunction'] = '\\' . get_class($control) . '::formatValue';
                }
            }
        }

        foreach ($this->_naviControls as $key => $control) {
            if (is_object($control)) {
                if ($control instanceof Element\ColumnInterface) {
                    $this->_naviControls[$key] = array('id' => $key);
                } else {
                    unset($this->_naviControls[$key]);
                }
            }
        }
        foreach ($this->_stateControls as $key => $control) {
            $this->_stateControls[$key] = array();
        }
        foreach ($this->_filterControls as $key => $control) {
            $this->_filterControls[$key] = array();
        }
        foreach ($this->_presentationControls as $key => $control) {
            $this->_presentationControls[$key] = array();
        }
    }

    /**
     * Zapisuje wewnętrzne obiekty klasy przy serializacji zbioru danych
     * 
     * @return array
     */
    public function __sleep() {
        Msg::add($this->getName() . '->' . __FUNCTION__);

        if (is_object($this->_dataSet)) {
            $this->_dataSet = serialize($this->_dataSet);
        }

        $this->_serializeControls();

        if (is_object($this->_form)) {
            $this->_form = get_class($this->_form);
        }

        if (is_object($this->_view)) {
            $this->_view = null;
        }

        $this->_state = self::STATE_SERIALIZED;
        Msg::add($this->getName() . '-> koniec usypiania');
        return array_keys(get_object_vars($this));
    }

    /**
     * Odczytuje wewnętrzne obiekty klasy przy deserializacji źródła danych
     * 
     * @return void
     */
    public function __wakeup() {
        Msg::add($this->getName() . '->' . __FUNCTION__);
        if (is_string($this->_dataSet)) {
            $this->_dataSet = unserialize($this->_dataSet);
        }
        if (is_string($this->_form)) {
            $form = ActionManager::getInstance()->getForm();
            if (is_object($form) && get_class($form) == $this->_form) {
                $this->_form = $form;
            } else {
                $this->_form = new $this->_form;
            }
        }

        $this->setView();
        $this->_state = self::STATE_UNSERIALIZED;
        Msg::add('Zakończona deserializacja źródła: ' . $this->getName());
    }

    /**
     * Pobiera właściwości obiektu z sesji
     * 
     * @param string|null $id
     * @return \ZendY\Db\DataSource
     */
    public function loadObject($id = null) {
        if (!isset($id) && ($this->getName()))
            $id = $this->getName();
        Msg::add($this->getName() . '->' . __FUNCTION__);
        $dbs = new \Zend_Session_Namespace('db');
        if (isset($dbs->datasource)) {
            if (isset($id)) {
                if (array_key_exists($id, $dbs->datasource)) {
                    $dataSource = unserialize($dbs->datasource[$id]);
                    $this->cloneThis($dataSource);
                }
            }
        }
        return $this;
    }

    /**
     * Zapisuje obiekt w sesji
     * 
     * @return \ZendY\Db\DataSource 
     */
    public function saveObject() {
        Msg::add('Stan źródła ' . $this->getName() . ' przed zapisaniem ' . $this->_state);
        if ($this->_state == self::STATE_CREATED || $this->_state == self::STATE_UNSERIALIZED) {
            Msg::add($this->getName() . '->' . __FUNCTION__);
            $dbs = new \Zend_Session_Namespace('db');
            $id = $this->getName();
            $dbs->form[$this->getFormId()][] = $id;
            $dbs->datasource[$id] = serialize($this);
            Msg::add($this->getName() . '-> Koniec ' . __FUNCTION__);
        }
        return $this;
    }

    /**
     * Renderowanie obiektu źródła danych
     * 
     * @return string|false
     */
    public function render() {
        $id = $this->getName();

        if (!$this->isRendered()) {
            $formId = $this->getFormId();
            $formClass = \ZendY\JQuery::encodeJson(explode('\\', get_class($this->getForm())));

            Msg::add(strtoupper('rozpoczynam renderowanie ' . $id));
            $result = '';
            $js[] = sprintf('var ds = new dataSource("%s","%s",%s,"%s","%s","%s");'
                    , $this->getName()
                    , self::$controller . self::$dataAction
                    , $formClass
                    , $formId
                    , $this->getDialog()
                    , $this->getView()->translate(Msg::MSG_ACTION_CONFIRM)
            );

            /**
             * okno dialogowe
             */
            if ($this->_dialog) {
                $text = sprintf('<img src="%s/library/components/dialog/ajax-loader.gif" /> <span>%s</span>'
                        , $this->getView()->host
                        , $this->getView()->translate(Msg::MSG_DATA_LOADING)
                );
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
                        $this->getName() . '_dialog'
                        , $text
                        , $params
                        , array('class' => Css::DIALOG_LOADING)
                );
            }

            /**
             * dołączanie kodu js odpowiedzialnego za stworzenie 
             * obiektów zarządzających kontrolkami po stronie przeglądarki
             */
            foreach ($this->_editControls as $control) {
                if ($control instanceof Element\CellInterface) {
                    $js[] = $control->renderDbCell();
                }
            }

            foreach ($this->_naviControls as $control) {
                $js[] = $control->renderDbNavi();
            }

            foreach ($this->_filterControls as $control) {
                $js[] = $control->renderDbFilter();
            }

            foreach ($this->_presentationControls as $control) {
                
            }

            foreach ($this->_stateControls as $control) {
                $js[] = $control->renderDbState();
            }

            $js[] = sprintf('df["%s"].addDataSource(ds);', $formId);

            if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
                $result = $result . '<script>' . implode("\n", $js) . '</script>';
            } else {
                $this->getView()->jQuery()
                        ->addJavascriptFile($this->getView()->host . '/library/components/datasource/datasource.js')
                        ->addJavascriptFile($this->getView()->host . '/library/components/datasource/dbedit.js')
                        ->addJavascriptFile($this->getView()->host . '/library/components/datasource/dbnavi.js')
                        ->addJavascriptFile($this->getView()->host . '/library/components/datasource/dbfilter.js')
                        ->addJavascriptFile($this->getView()->host . '/library/components/datasource/dbaction.js')
                        ->addJavascriptFile($this->getView()->host . '/library/components/datasource/dbexpr.js')
                ;
                $this->getView()->jQuery()->addOnLoad(implode("\n", $js));
            }
            $this->_rendered = true;

            Msg::add(strtoupper('koncze renderowanie ' . $id));
            return $result;
        } else {
            Msg::add($id . ' było już wyrenderowane');
            return false;
        }
    }

    /**
     * Zwraca informację czy źródło danych zostało wyrenderowane
     * 
     * @return bool
     */
    public function isRendered() {
        return $this->_rendered;
    }

    /**
     * Zwraca stan obiektu
     * 
     * @return int
     */
    public function getState() {
        return $this->_state;
    }

}