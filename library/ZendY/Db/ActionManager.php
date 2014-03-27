<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db;

use ZendY\Msg;
use ZendY\Db\DataSet;
use ZendY\Db\Form\Element as DbElement;

/**
 * Menedżer akcji na zbiorach danych - według wzorca Singleton
 *
 * @author Piotr Zając
 */
final class ActionManager {

    /**
     * Obiekt zapytania
     * 
     * @var \Zend_Controller_Request_Abstract 
     */
    protected $_request;

    /**
     * Tablica zapisanych źródeł danych
     * 
     * @var array
     */
    protected $_dataSources = array();

    /**
     * Nazwa akcji na zbiorze
     * 
     * @var string
     */
    protected $_dataAction;

    /**
     * Źródła danych, na których będzie wykonywana akcja
     * 
     * @var array 
     */
    protected $_dataSourceId;

    /**
     * Dane zwracane do przegladarki w wyniku wykonania akcji
     * 
     * @var array
     */
    protected $_resultData = array();

    /**
     * Przechowuje instancję klasy menedżera
     *
     * @var bool|object
     */
    private static $instance = false;

    /**
     * Formularz, do którego odnosi się akcja
     * 
     * @var \ZendY\Db\Form
     */
    protected $_form;

    /**
     * Blokada tworzenia obiektu spoza klasy
     * 
     * @return void
     */
    private function __construct() {
        
    }

    /**
     * Blokada kopiowania obiektu
     * 
     * @return void
     */
    private function __clone() {
        die(sprintf(Msg::MSG_SINGLETON_CLONE, __CLASS__));
    }

    /**
     * Inicjalizacja menedżera
     * 
     * @return bool
     */
    public function init() {
        Msg::add('ACTION MANAGER -> ' . __FUNCTION__);
        $dbs = new \Zend_Session_Namespace('db');
        $this->_request = \Zend_Controller_Front::getInstance()->getRequest();
        if (isset($dbs->datasource) && isset($dbs->form)) {
            if ($this->_request->getParam('dataAction')) {
                $this->_dataAction = $this->_request->getParam('dataAction');
            }
            if ($this->_request->getParam('id')) {
                $this->_dataSourceId = $this->_request->getParam('id');
                if (!is_array($this->_dataSourceId)) {
                    $this->_dataSourceId = array($this->_dataSourceId);
                }
            }
            Msg::add('Liczba źródeł ' . count($dbs->datasource));
            foreach ($dbs->datasource as $key => $dataSource) {
                if ((array_key_exists($key, $this->_dataSources)
                        && !is_object($this->_dataSources[$key]) || !array_key_exists($key, $this->_dataSources))
                        && $this->_request->getParam('formId')
                        && in_array($key, $dbs->form[$this->_request->getParam('formId')])) {
                    Msg::add('Deserializacja źródła: ' . $key);
                    $ds = unserialize($dataSource);
                    $this->_dataSources[$key] = $ds;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Zwraca instancję obiektu menedżera
     * 
     * @return \ZendY\Db\ActionManager
     */
    public static function getInstance() {
        if (self::$instance == false) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * Zwraca obiekt formularza
     * 
     * @return \Zend\Db\Form
     */
    public function getForm() {
        $formClass = $this->_request->getParam('form');
        if (!isset($this->_form) && class_exists($formClass)) {
            $this->_form = new $formClass();
            $this->_form->setName($this->_request->getParam('formId'));
        }

        return $this->_form;
    }

    /**
     * Zwraca nazwę akcji, która będzie wykonana na zbiorze danych
     * 
     * @return string
     */
    public function getDataAction() {
        return $this->_dataAction;
    }

    /**
     * Zwraca źródło danych o podanym id
     * 
     * @param string $id
     * @return \ZendY\Db\DataSource
     */
    public function getDataSource($id) {
        if (array_key_exists($id, $this->_dataSources))
            return $this->_dataSources[$id];
        else
            return NULL;
    }

    /**
     * Zwraca wszystkie źródła danych
     * 
     * @return array
     */
    public function getDataSources() {
        return $this->_dataSources;
    }

    /**
     * Zwraca podrzędne żródła danych powiązane relacją master-detail
     * 
     * @param \ZendY\Db\DataSource $masterSource
     * @return array 
     */
    protected function _getDetailSources($masterSource) {
        Msg::add($masterSource->getName() . '->' . __FUNCTION__);
        $details = array();
        foreach ($this->_dataSources as $dataSource) {
            $dataSet = $dataSource->getDataSet();
            $masterSet = $masterSource->getDataSet();
            if ($dataSet instanceof DataSet\Base
                    && $dataSet->hasMaster()
                    && $dataSet->isDetailSet($masterSet)) {
                $details[] = $dataSource;
            }
        }
        return $details;
    }

    /**
     * Zwraca źródła danych powiązane z danym formularzem
     * 
     * @param ZendY\Db\DataSource|null $masterSource
     * @param string|null $formId
     * @return array
     */
    public function getMasterDetailSources($masterSource = null, $formId = null) {
        $result = array();
        if (!isset($masterSource)) {
            if (isset($this->_dataSourceId)) {
                foreach ($this->_dataSourceId as $dataSourceId) {
                    $masterSource[] = $this->_dataSources[$dataSourceId];
                }
            } else {
                if (isset($formId)) {
                    foreach ($this->_dataSources as $dataSource) {
                        if ($dataSource->getFormId() == $formId)
                            $result[$dataSource->getName()] = $dataSource;
                    }
                }
                else {
                    $result = $this->_dataSources;
                    Msg::add('Brakuje id formularza, wstawiam wszystkie źródła');
                }
                return $result;
            }
        }
        if (!is_array($masterSource)) {
            $masterSource = array($masterSource);
        }

        foreach ($masterSource as $source) {
            //filtrowanie po źródłach przypisanych do podanego formularza
            if (isset($formId)) {
                if ($source->getFormId() == $formId)
                    $result[$source->getName()] = $source;
            } else {
                $result[$source->getName()] = $source;
            }
            $detailSources = $this->_getDetailSources($source);
            foreach ($detailSources as $dataSource) {
                $result = array_merge($result, $this->getMasterDetailSources($dataSource, $formId));
            }
        }
        return $result;
    }

    /**
     * Odświeża dane zbiorów podrzędnych
     * 
     * @param \ZendY\Db\DataSource|null $masterSource
     * @return bool
     */
    protected function _refilterDetail($masterSource = null) {
        Msg::add($masterSource->getName() . '->' . __FUNCTION__);
        if (!isset($masterSource)) {
            if (isset($this->_dataSourceId))
                foreach ($this->_dataSourceId as $dataSourceId) {
                    $masterSource[] = $this->_dataSources[$dataSourceId];
                }
            else
                return FALSE;
        }
        if (!is_array($masterSource)) {
            $masterSource = array($masterSource);
        }

        foreach ($masterSource as $source) {
            $detailSources = $this->_getDetailSources($source);
            if (count($detailSources)) {
                foreach ($detailSources as $detailSource) {
                    $detailSource->getDataSet()->closeAction(array(), true);
                    $detailSource->getDataSet()->openAction(array('first' => true), false);
                    $this->_refilterDetail($detailSource);
                }
            }
        }
        return TRUE;
    }

    /**
     * Sprawdza czy użytkownik ma nadane uprawnienie do wskazanej akcji
     * 
     * @param \Zend_Acl_Resource_Interface|string $resource
     * @param string $privilege
     * @return bool
     */
    public static function allowed($resource, $privilege = null) {
        $result = false;
        $front = \Zend_Controller_Front::getInstance();
        if ($front->hasPlugin('ZendY\Controller\Plugin\Acl')) {
            $acl = $front->getPlugin('ZendY\Controller\Plugin\Acl')->getAcl();
            if ($acl->has($resource)) {
                if ($acl->isAllowed(\Zend_Registry::get('role'), $resource, $privilege)) {
                    $result = true;
                }
            } else {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Wykonuje akcję opisaną zapytaniem request
     * 
     * @return array
     */
    public function action() {
        Msg::add('ACTION MANAGER -> ' . __FUNCTION__);
        $result = array();
        //jeśli akcja dotyczy konkretnego zbioru lub zbiorów
        if (isset($this->_dataSourceId)) {
            foreach ($this->_dataSourceId as $dataSourceId) {
                if (isset($this->_dataSources[$dataSourceId])) {
                    $dataSet = $this->_dataSources[$dataSourceId]->getDataSet();
                    if (isset($dataSet)) {
                        $dataAction = $this->_dataAction;
                        $resource = $dataSet->getName();
                        $privilege = $dataSet->getActionPrivilege($this->_dataAction);
                        if (self::allowed($resource, $privilege)) {
                            if ($dataSet->isRegisteredAction($dataAction)) {
                                //przekazanie obiektu formularza do akcji "zapisz" w celu szybszej walidacji
                                if ($dataAction == DataSet\Editable::ACTION_SAVE) {
                                    $this->_request->setParam('form', $this->getForm());
                                }
                                $result = $dataSet->$dataAction($this->_request->getParams());
                                if (array_key_exists('messages', $result)) {
                                    $this->_resultData[$dataSourceId]['messages'] = $result['messages'];
                                    unset($result['messages']);
                                }
                                if (array_key_exists('errors', $result)) {
                                    $this->_resultData[$dataSourceId]['errors'] = $result['errors'];
                                    unset($result['errors']);
                                }
                                $this->_refilterDetail($this->_dataSources[$dataSourceId]);
                            } else {
                                $this->_resultData[$dataSourceId]['errors'][] = sprintf(Msg::MSG_ACTION_NO_ACTION, $dataAction);
                            }
                        } else {
                            $this->_resultData[$dataSourceId]['errors'][] = sprintf(Msg::MSG_ACTION_NO_PERMISSION, $dataAction);
                        }
                    }
                }
            }
        } elseif ($this->_dataAction == 'init') {
            //otwiera wszystkie źródła z danego formularza
            foreach ($this->_dataSources as $dataSource) {
                $dataSet = $dataSource->getDataSet();
                if (isset($dataSet) && $dataSource->getFormId() == $this->_request->getParam('formId')) {
                    $result = array_merge($result, $dataSet->openAction($this->_request->getParams()));
                }
            }
        }
        return $result;
    }

    /**
     * Zwraca wartości z tych pól rekordu do których są podłączone kontrolki edycyjne
     * 
     * @param string $dataSourceId
     * @param array $record
     * @return array
     */
    protected function _getEditControlsData($dataSourceId, array $record) {
        Msg::add('ACTION MANAGER -> ' . __FUNCTION__);
        $editDataValues = array();
        $editControls = $this->_dataSources[$dataSourceId]->getEditControls();
        $dataSet = $this->_dataSources[$dataSourceId]->getDataSet();
        //niezaleznie od kontrolek zwracamy wartości klucza w bieżącym rekordzie
        foreach ($dataSet->getPrimary() as $key) {
            $editDataValues[$key] = $record[$key];
        }
        //zwracamy wartości pól, dla których istnieją powiązane kontrolki
        foreach ($editControls as $name => $control) {
            $dataField = $control['dataField'];
            if (array_key_exists($dataField, $record)) {
                if (isset($control['formatValueFunction'])) {
                    $editDataValues[$name] = call_user_func($control['formatValueFunction'], $record[$dataField]);
                } else {
                    $editDataValues[$name] = $record[$dataField];
                }
            }
        }
        return $editDataValues;
    }

    /**
     * Zwraca wartości z tych pól zbioru do których są podłączone kontrolki nawigacyjne (listy)
     * 
     * @param string $dataSourceId
     * @return array
     */
    protected function _getNaviControlsData($dataSourceId) {
        Msg::add('ACTION MANAGER -> ' . __FUNCTION__);
        $naviControls = $this->_dataSources[$dataSourceId]->getNaviControls();
        $dataSet = $this->_dataSources[$dataSourceId]->getDataSet();
        $offset = null;
        $recordPerPage = null;
        $controlsData = array();
        if ($dataSet->getPage() && $dataSet->getRecordPerPage()) {
            $recordPerPage = $dataSet->getRecordPerPage();
            $offset = ($dataSet->getPage() - 1) * $dataSet->getRecordPerPage();
        }
        $params = $this->_request->getParams();
        foreach ($naviControls as $name => $options) {
            $control = $this->getForm()->getNestedElement($name);
            if (isset($control) && $control instanceof DbElement\ListInterface) {
                if ($control instanceof DbElement\CalendarInterface) {
                    $control->refreshPeriod($params);
                    if ($options['list'] == 'event') {
                        //print($this->_dataSources[$dataSourceId]->getDataSet()->getPeriod()->getBegin() . ' ');
                        //print($control->getPeriod()->getEnd() . ' ');
                        //exit;
                    }
                    if (isset($params[\ZendY\Form\Element\Calendar::PARAM_RANGE])) {
                        $this->_dataSources[$dataSourceId]
                                ->getDataSet()
                                ->setPeriod($control->getPeriod());
                    }
                }

                $controlsData[$name]['list'] = $options['list'];
                $controlsData[$name]['data'] = $control->formatData(
                        $this->_dataSources[$dataSourceId]
                                ->getDataSet()
                                ->getItems(
                                        $offset
                                        , $recordPerPage
                                        , $control->getFields($options['list'])
                                        , $control->getConditionalRowFormats($options['list'])
                                ));
            }
        }
        return $controlsData;
    }

    /**
     * Zwraca dane w wyniku wykonania akcji
     * 
     * @return array
     */
    public function getResultData() {
        Msg::add('ACTION MANAGER -> ' . __FUNCTION__);
        if ($this->_request->getParam('formId')) {
            $formId = $this->_request->getParam('formId');
        } else {
            $formId = null;
        }
        $masterDetail = $this->getMasterDetailSources(null, $formId);
        Msg::add('Stany zbiorów powiązanych po wykonaniu akcji');
        foreach ($masterDetail as $dataSource) {
            $dataSet = $dataSource->getDataSet();
            if (isset($dataSet)) {
                $id = $dataSource->getName();
                $this->_resultData[$id]['data'] = array();

                if (in_array($dataSet->getState(), array(DataSet\Base::STATE_VIEW, DataSet\Editable::STATE_EDIT))
                        || ($this->_dataAction == DataSet\Editable::ACTION_ADDCOPY)) {
                    //dane list są odswieżane tylko przy niektórych akcjach
                    if (!isset($this->_dataSourceId)
                            || (isset($this->_dataSourceId)
                            && (in_array($id, $this->_dataSourceId))
                            && $dataSet->isRefreshAction($this->_dataAction))
                            || (isset($this->_dataSourceId)
                            && (!in_array($id, $this->_dataSourceId)))) {
                        $this->_resultData[$id]['multi'] = $this->_getNaviControlsData($id);
                    }
                    if ($dataSet->getRecordCount()) {
                        $current = $dataSet->getCurrent(true);
                        $this->_resultData[$id]['data'] = $this->_getEditControlsData($id, $current);
                    }
                }
                $this->_resultData[$id]['expr'] = array(
                    DataSet\Base::EXPR_OFFSET => $dataSet->getOffset(),
                    DataSet\Base::EXPR_COUNT => $dataSet->getRecordCount(),
                    DataSet\Base::EXPR_STATE => $dataSet->getState(),
                    DataSet\Base::EXPR_PAGE => $dataSet->getPage(),
                    DataSet\Base::EXPR_PAGECOUNT => $dataSet->getPageCount()
                );

                $this->_resultData[$id]['filter'] = $dataSet->getFilters();
                $this->_resultData[$id]['sort'] = $dataSet->getSorts();
                $this->_resultData[$id]['navigator'] = $dataSet->getNavigator();
            }
        }

        foreach ($masterDetail as $dataSource) {
            //zapisuje obiekt
            $dataSource->saveObject();
        }
        //Msg::add('ACTION MANAGER -> ' . __FUNCTION__ . ' Koniec');
        //$this->_resultData['msg'] = Msg::getMessages();
        return $this->_resultData;
    }

}