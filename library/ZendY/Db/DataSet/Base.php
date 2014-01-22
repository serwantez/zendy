<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Msg;
use ZendY\Component;
use ZendY\Db\Filter;
use ZendY\Db\Sort;
use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Report\PrintDataSet;
use ZendY\Exception;

/**
 * Klasa bazowa zbioru danych
 *
 * @author Piotr Zając
 */
abstract class Base extends Component {
    /*
     * Typy akcji na zbiorach
     */

    const ACTIONTYPE_STANDARD = 'standard';
    const ACTIONTYPE_EDIT = 'edit';
    const ACTIONTYPE_FILTER = 'filter';
    const ACTIONTYPE_SAVE = 'save';
    const ACTIONTYPE_CONFIRM = 'confirm';
    const ACTIONTYPE_GENERATEFILE = 'generateFile';
    const ACTIONTYPE_GENERATEWEBPAGE = 'generateWebpage';

    /**
     * Typy uprawnień powiązane z akcjami
     */
    const ACTION_PRIVILEGE_VIEW = 'view';
    const ACTION_PRIVILEGE_EDIT = 'edit';
    const ACTION_PRIVILEGE_DELETE = 'delete';

    /*
     * Akcje na zbiorze
     */
    const ACTION_OPEN = 'openAction';
    const ACTION_CLOSE = 'closeAction';
    const ACTION_FIRST = 'firstAction';
    const ACTION_LAST = 'lastAction';
    const ACTION_PREVIOUS = 'previousAction';
    const ACTION_NEXT = 'nextAction';
    const ACTION_SEEK = 'seekAction';
    const ACTION_SEARCH = 'searchAction';
    const ACTION_FILTER = 'filterAction';
    const ACTION_CLEARFILTER = 'clearfilterAction';
    const ACTION_FILTERSEEK = 'filterSeekAction';
    const ACTION_FILTERSEARCH = 'filterSearchAction';
    const ACTION_SORT = 'sortAction';
    const ACTION_REFRESH = 'refreshAction';
    const ACTION_FIRSTPAGE = 'firstPageAction';
    const ACTION_LASTPAGE = 'lastPageAction';
    const ACTION_PREVIOUSPAGE = 'previousPageAction';
    const ACTION_NEXTPAGE = 'nextPageAction';
    const ACTION_SEEKPAGE = 'seekPageAction';
    const ACTION_EXPORTEXCEL = 'exportExcelAction';
    const ACTION_PRINT = 'printAction';

    /**
     * Numer bieżącego rekordu
     */
    const EXPR_OFFSET = 'offset';
    /**
     * Liczba rekordów
     */
    const EXPR_COUNT = 'count';
    /**
     * Stan zbioru
     */
    const EXPR_STATE = 'state';
    /**
     * Numer strony danych
     */
    const EXPR_PAGE = 'page';
    /**
     * Liczba stron danych
     */
    const EXPR_PAGECOUNT = 'pageCount';
    /**
     * Stan zbioru wyłączonego
     */
    const STATE_OFF = 0;
    /**
     * Stan przeglądania rekordów
     */
    const STATE_VIEW = 1;

    /**
     * Operatory porównania
     */
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_NOT_EQUAL = 'not_equal';
    const OPERATOR_GREATER = 'greater';
    const OPERATOR_LESS = 'less';
    const OPERATOR_GREATER_EQUAL = 'greater_equal';
    const OPERATOR_LESS_EQUAL = 'less_equal';
    const OPERATOR_BEGIN = 'begin';
    const OPERATOR_NOT_BEGIN = 'not_begin';
    const OPERATOR_END = 'end';
    const OPERATOR_NOT_END = 'not_end';
    const OPERATOR_CONTAIN = 'contain';
    const OPERATOR_NOT_CONTAIN = 'not_contain';
    const OPERATOR_IN = 'in';
    const OPERATOR_NOT_IN = 'not_in';
    const OPERATOR_IS_NULL = 'is_null';
    const OPERATOR_IS_NOT_NULL = 'is_not_null';
    /**
     * Łączniki
     */
    const CONNECTOR_AND = 'and';
    const CONNECTOR_OR = 'or';

    /**
     * Typ zwracanych danych
     */
    const RESULT_ASSOC = 'assoc';
    const RESULT_OBJ = 'obj';

    /**
     * Filtr na zbiorze danych
     * 
     * @var \ZendY\Db\Filter
     */
    protected $_filter;

    /**
     * Reguły sortowania
     * 
     * @var \ZendY\Db\Sort
     */
    protected $_order;

    /**
     * Pola klucza głównego
     * 
     * @var array
     */
    protected $_primary;

    /**
     * Nadrzędne źródło danych w relacji master-detail
     * 
     * @var \ZendY\Db\DataSource|null
     */
    protected $_masterSource = null;

    /**
     * Pole w nadrzędnym zbiorze danych w relacji master-detail
     * 
     * @var string 
     */
    protected $_masterField;

    /**
     * Operator porównania z wartością z nadrzędnego zbioru danych
     * 
     * @var string
     */
    protected $_masterOperator = self::OPERATOR_EQUAL;

    /**
     * Porównywane wyrażenie, którego $_masterField jest parametrem
     * 
     * @var string|null
     */
    protected $_masterExpr = null;

    /**
     * Pole w bieżącym zbiorze danych 
     * powiązane relacją master-detail ze zbiorem nadrzędnym
     * 
     * @var string
     */
    protected $_indexField;

    /**
     * Numer bieżącego wiersza w zbiorze
     * 
     * @var int
     */
    protected $_offset = -1;

    /**
     * Stan zbioru
     * 
     * @var int
     */
    protected $_state = self::STATE_OFF;

    /**
     * Liczba rekordów w zbiorze
     * 
     * @var int
     */
    protected $_recordCount = 0;

    /**
     * Liczba rekordów na stronie przy stronicowaniu danych
     * 
     * @var int
     */
    protected $_recordPerPage = 0;

    /**
     * Numer strony danych przy stronicowaniu
     * 
     * @var int 
     */
    protected $_page = 1;

    /**
     * Stany przycisków nawigacji po rekordach
     * 
     * @var array 
     */
    protected $_navigator = array();

    /**
     * Czy zbiór danych jest tylko do odczytu
     * 
     * @var bool
     */
    protected $_readOnly = false;

    /**
     * Tablica możliwych akcji na zbiorze
     * 
     * @var array
     */
    protected $_actions = array();

    /**
     * Inicjalizuje obiekt
     * 
     * @return void
     */
    public function init() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        parent::init();
        $this->_filter = new Filter();
        $this->_order = new Sort();
        $this->_registerActions();
    }

    /**
     * Ustawia parametry pojedynczej akcji
     * 
     * @param string $name
     * @param string $type
     * @param array $icon
     * @param string $text
     * @param string $shortKey
     * @param bool $refresh Informacja czy po wykonaniu akcji należy odświeżyć kontrolki zawierające cały zbiór
     * @return void
     */
    protected function _registerAction($name
    , $type = self::ACTIONTYPE_STANDARD
    , $icon = array()
    , $text = ''
    , $shortKey = null
    , $refresh = false
    , $privilege = self::ACTION_PRIVILEGE_VIEW) {
        $this->_actions[$name] = array(
            'icon' => $icon,
            'refresh' => $refresh,
            'text' => $text,
            'type' => $type,
            'privilege' => $privilege
        );
        if (isset($shortKey)) {
            $this->_actions[$name]['shortKey'] = $shortKey;
        }
    }

    /**
     * Ustawia parametry akcji
     * 
     * @return \ZendY\Db\DataSet\Base
     */
    protected function _registerActions() {
        $this->_registerAction(
                self::ACTION_OPEN
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_FOLDEROPEN)
                , 'Open'
        );
        $this->_registerAction(
                self::ACTION_CLOSE
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_FOLDERCOLLAPSED)
                , 'Close'
        );
        $this->_registerAction(
                self::ACTION_FIRST
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEEKFIRST)
                , 'First'
        );
        $this->_registerAction(
                self::ACTION_LAST
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEEKEND)
                , 'Last'
        );
        $this->_registerAction(
                self::ACTION_NEXT
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEEKNEXT)
                , 'Next'
        );
        $this->_registerAction(
                self::ACTION_PREVIOUS
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEEKPREV)
                , 'Previous'
        );
        $this->_registerAction(
                self::ACTION_REFRESH
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_REFRESH)
                , 'Refresh'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_SEEK
                , self::ACTIONTYPE_STANDARD
                , null
                , 'Seek'
        );
        $this->_registerAction(
                self::ACTION_SORT
                , self::ACTIONTYPE_STANDARD
                , null
                , 'Sort'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_SEARCH
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEARCH)
                , 'Search'
        );
        $this->_registerAction(
                self::ACTION_FILTER
                , self::ACTIONTYPE_FILTER
                , array('primary' => Css::ICON_SEARCH)
                , 'Filter'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_CLEARFILTER
                , self::ACTIONTYPE_FILTER
                , array('primary' => Css::ICON_CLOSE)
                , 'Clear filter'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_FILTERSEEK
                , self::ACTIONTYPE_FILTER
                , array('primary' => Css::ICON_SEARCH)
                , 'Filter & Seek'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_FILTERSEARCH
                , self::ACTIONTYPE_FILTER
                , array('primary' => Css::ICON_SEARCH)
                , 'Filter & Search'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_FIRSTPAGE
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEEKFIRST)
                , 'First page'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_LASTPAGE
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEEKEND)
                , 'Last page'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_NEXTPAGE
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEEKNEXT)
                , 'Next page'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_PREVIOUSPAGE
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_SEEKPREV)
                , 'Previous page'
                , null
                , TRUE
        );
        $this->_registerAction(
                self::ACTION_SEEKPAGE
                , self::ACTIONTYPE_STANDARD
                , null
                , 'Seek page'
                , null
                , TRUE);
        $this->_registerAction(
                self::ACTION_EXPORTEXCEL
                , self::ACTIONTYPE_GENERATEFILE
                , array('primary' => Css::ICON_CALCULATOR)
                , 'Export to Excel'
        );
        $this->_registerAction(
                self::ACTION_PRINT
                , self::ACTIONTYPE_GENERATEWEBPAGE
                , array('primary' => Css::ICON_PRINT)
                , 'Print'
        );
        return $this;
    }

    /**
     * Zwraca nazwy zarejestrowanych akcji na zbiorze
     * 
     * @return array
     */
    public function getActions() {
        return array_keys($this->_actions);
    }

    /**
     * Zwraca parametr wybranej akcji
     * 
     * @param string $action
     * @param string $param
     * @return mixed
     * @throws Exception
     */
    public function getActionParam($action, $param) {
        if ($this->isRegisteredAction($action)
                && array_key_exists($param, $this->_actions[$action])) {
            return $this->_actions[$action][$param];
        } else {
            throw new Exception(sprintf("Action '%s' has not registered", $action));
            return NULL;
        }
    }

    /**
     * Zwraca wszystkie parametry wybranej akcji
     * 
     * @param string $action
     * @return array|null
     */
    public function getActionParams($action) {
        if ($this->isRegisteredAction($action)) {
            return $this->_actions[$action];
        } else {
            throw new Exception(sprintf("Action '%s' has not registered", $action));
            return NULL;
        }
    }

    /**
     * Ustawia stan akcji, czyli informację, czy dana akcja może zostać wykonana
     * 
     * @param array $params
     * @return \ZendY\Db\DataSet\Base
     */
    protected function _setActionState($params = array()) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $this->_navigator[self::ACTION_FIRST] = ($this->_state == self::STATE_VIEW
                && $this->_offset > 0);
        $this->_navigator[self::ACTION_PREVIOUS] = ($this->_state == self::STATE_VIEW
                && $this->_offset > 0);
        $this->_navigator[self::ACTION_LAST] = ($this->_state == self::STATE_VIEW
                && $this->_offset < $this->_recordCount - 1);
        $this->_navigator[self::ACTION_NEXT] = ($this->_state == self::STATE_VIEW
                && $this->_offset < $this->_recordCount - 1);
        $this->_navigator[self::ACTION_FILTER] = ($this->_state >= self::STATE_VIEW);
        $this->_navigator[self::ACTION_CLEARFILTER] = ($this->_state >= self::STATE_VIEW
                && count($this->getFilters()) > 0);
        $this->_navigator[self::ACTION_SEARCH] = $this->_navigator[self::ACTION_FILTER];
        $this->_navigator[self::ACTION_REFRESH] = ($this->_state >= self::STATE_VIEW);
        $this->_navigator[self::ACTION_EXPORTEXCEL] = ($this->_state == self::STATE_VIEW);
        $this->_navigator[self::ACTION_PRINT] = ($this->_state == self::STATE_VIEW);
        $page = $this->getPage();
        $pageCount = $this->getPageCount();
        $this->_navigator[self::ACTION_FIRSTPAGE] = ($this->_state == self::STATE_VIEW
                && $page > 1);
        $this->_navigator[self::ACTION_PREVIOUSPAGE] = ($this->_state == self::STATE_VIEW
                && $page > 1);
        $this->_navigator[self::ACTION_LASTPAGE] = ($this->_state == self::STATE_VIEW
                && $page < $pageCount);
        $this->_navigator[self::ACTION_NEXTPAGE] = ($this->_state == self::STATE_VIEW
                && $page < $pageCount);
        return $this;
    }

    /**
     * Zwraca informację o tym, czy podana akcja wymaga odświeżenia kontrolek z rekordami z całego zbioru
     * 
     * @param string $action
     * @return bool
     */
    public function isRefreshAction($action) {
        return $this->getActionParam($action, 'refresh');
    }

    /**
     * Zwraca informację o tym, czy podana akcja jest zarejestrowana
     * 
     * @param string $action
     * @return bool
     */
    public function isRegisteredAction($action) {
        return array_key_exists($action, $this->_actions);
    }

    /**
     * Zwraca nazwę uprawnienia potrzebnego do wykonania akcji
     * 
     * @param string $action
     * @return string
     */
    public function getActionPrivilege($action) {
        return $this->getActionParam($action, 'privilege');
    }

    /**
     * Wyłuskuje nazwę akcji z nazwy metody zbioru danych
     * 
     * @param string $action
     * @return string
     */
    public static function getActionName($action) {
        $actionName = substr($action, 0, strpos($action, 'Action'));
        return $actionName;
    }

    /**
     * Zapisuje wewnętrzne obiekty klasy przy serializacji zbioru danych
     * 
     * @return array
     */
    public function __sleep() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        if ($this->hasMaster() && is_object($this->_masterSource)) {
            $this->_masterSource = (string) $this->_masterSource->getId();
        }
        Msg::add($this->getId() . '-> koniec usypiania');
        return array_keys(get_object_vars($this));
    }

    /**
     * Wczytuje wewnętrzne obiekty klasy przy deserializacji zbioru danych
     * 
     * @return void
     */
    public function __wakeup() {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        if ($this->hasMaster() && is_string($this->_masterSource)) {
            $this->_masterSource = \ZendY\Db\ActionManager::getInstance()->getDataSource($this->_masterSource);
        }
    }

    /**
     * Ustawia czy zbiór danych ma być tylko do odczytu
     * 
     * @param bool $readOnly
     * @return \ZendY\Db\DataSet\Base
     */
    public function setReadOnly($readOnly) {
        $this->_readOnly = $readOnly;
        return $this;
    }

    /**
     * Zwraca informację, czy zbiór danych ma być tylko do odczytu 
     * 
     * @return bool
     */
    public function getReadOnly() {
        return $this->_readOnly;
    }

    /**
     * Ustawia nadrzędne źródło danych 
     * dla relacji master-detail
     * 
     * @param \ZendY\Db\DataSource $masterSource
     * @return \ZendY\Db\DataSet\Base
     */
    public function setMasterSource(DataSource $masterSource) {
        $this->_masterSource = $masterSource;
        return $this;
    }

    /**
     * Zwraca nadrzędne źródło danych
     * 
     * @return \ZendY\Db\DataSource|null
     */
    public function getMasterSource() {
        return $this->_masterSource;
    }

    /**
     * Ustawia pole w nadrzędnym zbiorze danych
     * dla relacji master-detail
     * 
     * @param string $field
     * @return \ZendY\Db\DataSet\Base
     */
    public function setMasterField($field) {
        $this->_masterField = $field;
        return $this;
    }

    /**
     * Zwraca pole w nadrzędnym zbiorze danych
     * dla relacji master-detail
     * 
     * @return string|null
     */
    public function getMasterField() {
        return $this->_masterField;
    }

    /**
     * Ustawia operator porównania z wartością z nadrzędnego zbioru danych
     * dla relacji master-detail
     * 
     * @param string $operator
     * @return \ZendY\Db\DataSet\Base
     */
    public function setMasterOperator($operator) {
        $this->_masterOperator = $operator;
        return $this;
    }

    /**
     * Zwraca operator porównania z wartością z nadrzędnego zbioru danych
     * dla relacji master-detail
     * 
     * @return string|null
     */
    public function getMasterOperator() {
        return $this->_masterOperator;
    }

    /**
     * Ustawia wyrażenie porównania
     * dla relacji master-detail
     * 
     * @param string $expr
     * @return \ZendY\Db\DataSet\Base
     */
    public function setMasterExpr($expr) {
        $this->_masterExpr = $expr;
        return $this;
    }

    /**
     * Zwraca wyrażenie porównania
     * dla relacji master-detail
     * 
     * @return string|null
     */
    public function getMasterExpr() {
        return $this->_masterExpr;
    }

    /**
     * Ustawia pole w bieżącym zbiorze danych 
     * powiązane relacją master-detail ze zbiorem nadrzędnym
     * 
     * @param string|array $field
     * @return \ZendY\Db\DataSet\Base
     */
    public function setIndexField($field) {
        $this->_indexField = $field;
        return $this;
    }

    /**
     * Zwraca pole w bieżącym zbiorze danych
     * powiązane relacją master-detail ze zbiorem nadrzędnym
     * 
     * @return string|array|null
     */
    public function getIndexField() {
        return $this->_indexField;
    }

    /**
     * Zwraca informację o posiadaniu zbioru nadrzędnego w relacji master-detail
     * 
     * @return bool
     */
    public function hasMaster() {
        return (isset($this->_masterSource)
                && isset($this->_masterField)
                && isset($this->_indexField));
    }

    /**
     * Sprawdza czy podany zbiór jest zbiorem nadrzędnym
     * 
     * @param \ZendY\Db\DataSet\Base $masterSet
     * @return bool
     */
    public function isDetailSet(Base $masterSet) {
        $id = $this->_masterSource->getDataSet()->getId();
        return ($id == $masterSet->getId());
    }

    /**
     * Zwraca numer rekordu
     * 
     * @return int
     */
    public function getOffset() {
        return $this->_offset;
    }

    /**
     * Zwraca informacje o stanach przycisków nawigacyjnych
     * 
     * @return array
     */
    public function getNavigator() {
        return $this->_navigator;
    }

    /**
     * Ustawia stan zbioru
     * 
     * @param int $state 
     * @return \ZendY\Db\DataSet\Base
     */
    public function setState($state) {
        $this->_state = $state;
        return $this;
    }

    /**
     * Zwraca informację o stanie (otwarciu) zbioru
     * 
     * @return int
     */
    public function getState() {
        return $this->_state;
    }

    /**
     * Zwraca liczbe rekordów w zbiorze
     * 
     * @return int
     */
    public function getRecordCount() {
        return (int) $this->_recordCount;
    }

    /**
     * Czyści sortowanie
     * 
     * @return \ZendY\Db\DataSet\Base
     */
    public function clearSort() {
        $this->_order->clearSort();
        return $this;
    }

    /**
     * Zwraca informacje o sortowaniu tabeli
     * 
     * @return array
     */
    public function getSorts() {
        return $this->_order->getSorts();
    }

    /**
     * Ustawia klucz główny
     * 
     * @param string|array $name
     * @return \ZendY\Db\DataSet\Base
     */
    public function setPrimary($primary) {
        if (!is_array($primary))
            $primary = array(1 => $primary);
        $this->_primary = $primary;
        return $this;
    }

    /**
     * Zwraca pola klucza głównego
     * 
     * @return array
     */
    public function getPrimary() {
        $primary = $this->_primary;
        if (!isset($primary)) {
            $primary = $this->getColumns();
        }
        return $primary;
    }

    /**
     * Czyści filtrowanie
     * 
     * @param string $filterName
     * @return \ZendY\Db\DataSet\Base
     */
    public function clearFilter($filterName = null) {
        $this->closeAction(array(), true);
        $this->_filter->clearFilter($filterName);
        $this->openAction(array('first' => true), true);
        return $this;
    }

    /**
     * Czyści relację master-detail
     * 
     * @return \ZendY\Db\DataSet\Base
     */
    public function clearMaster() {
        $this->_filter->clearFilter('master');
        return $this;
    }

    /**
     * Zwraca informacje o filtrowaniu zbioru
     * 
     * @return array
     */
    public function getFilters() {
        return $this->_filter->getFilters();
    }

    /**
     * Ustawia liczbę rekordów na stronę
     * 
     * @param int $rpp
     * @return \ZendY\Db\DataSet\Base
     */
    public function setRecordPerPage($rpp) {
        if ($rpp > 0)
            $this->_recordPerPage = $rpp;
        return $this;
    }

    /**
     * Zwraca liczbę rekordów na stronę
     * 
     * @return int
     */
    public function getRecordPerPage() {
        return $this->_recordPerPage;
    }

    /**
     * Ustawia numer strony
     * 
     * @param int $page
     * @return \ZendY\Db\DataSet\Base
     */
    public function setPage($page) {
        if ($page > 0)
            $this->_page = $page;
        return $this;
    }

    /**
     * Zwraca numer strony
     * 
     * @return int
     */
    public function getPage() {
        $pc = $this->getPageCount();
        if ($this->_page > $pc && $pc > 0)
            $this->_page = $pc;
        return $this->_page;
    }

    /**
     * Zwraca liczbę wszystkich stron
     * 
     * @return int
     */
    public function getPageCount() {
        if ($this->getRecordCount() && $this->getRecordPerPage()) {
            return ceil($this->getRecordCount() / $this->getRecordPerPage());
        } else {
            return 1;
        }
    }

    /**
     * Pobiera pierwszy wiersz podanej kolumny
     * 
     * @param string $col
     * @return string
     */
    public function fetchOne($col) {
        $cur = $this->getCurrent(false);
        return $cur[$col];
    }

    /**
     * Wyszukuje i zwraca rekordy których podane pole jest równe podanej wartości
     * 
     * @param string $field
     * @param string $value
     * @param bool $returnOneRow
     * @return array
     */
    public function findBy($field, $value, $returnOneRow = false) {
        $filter = new Filter();
        $filter->addFilter($field, $value);
        $this->filterAction(array('filter' => $filter));
        if ($returnOneRow) {
            $result = $this->getItems(0, 1);
            if (isset($result[0]))
                $result = $result[0];
        } else {
            $result = $this->getItems();
        }
        return $result;
    }

    /**
     * Zwraca bieżący rekord
     * 
     * @return array
     */
    abstract public function getCurrent();

    /**
     * Pobiera rekordy limitowane
     * 
     * @param int $offset
     * @param int $itemCount
     * @param null|array|string $columns
     * @param null|array $conditionalFormats
     * @return array
     */
    abstract public function getItems($offset = null, $itemCount = null, $columns = null, $conditionalFormats = null);

    /**
     * Oblicza i zwraca liczbę wszystkich rekordów w zbiorze
     * 
     * @return int
     */
    abstract protected function _count();

    /**
     * Zwraca wszystkie zdefiniowane pola (kolumny) zbioru
     * 
     * @return array
     */
    abstract public function getColumns();

    /** AKCJE */

    /**
     * Otwiera zbiór danych
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function openAction($params = array('first' => true), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        if (!array_key_exists('first', $params)) {
            $params['first'] = true;
        }
        $result = array();
        if ($this->hasMaster()) {
            Msg::add($this->getId() . ' ma mastera');
            $masterSet = $this->_masterSource->getDataSet();
            $indexField = $this->_indexField;
            //pole przekazane jako tablica: domena,nazwa pola,alias
            if (is_array($indexField)) {
                $indexField = $indexField[2];
            }
            //zbiór nadrzędny master jest otwarty
            if ($masterSet->getState()) {
                if ($this->_state)
                    $result = $this->closeAction(null, true);
                $cur = $masterSet->getCurrent();

                if (array_key_exists($this->_masterField, $cur)) {
                    Msg::add($this->getId() . ' będzie przefiltrowany');
                    if ($this->getMasterExpr() != null) {
                        $value = new \Zend_Db_Expr(sprintf($this->getMasterExpr(), $cur[$this->_masterField]));
                    } else {
                        $value = $cur[$this->_masterField];
                    }
                    $this->_filter->setFilter('master', array(
                        $indexField => array(
                            'value' => $value,
                            'operator' => $this->getMasterOperator()
                        )
                    ));
                }
            }
            //zbiór master jest zamknięty
            else {
                $result = $this->closeAction(null, true);
                $this->_filter->setFilter('master', array($indexField => ''));
            }
        }

        if ($this->_state == self::STATE_OFF) {
            $this->_recordCount = $this->_count();
            $this->_state = self::STATE_VIEW;
            $this->_offset = -1;
            if ($params['first'])
                $result = array_merge($result, $this->firstAction(null, true));

            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Zamyka zbiór danych
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function closeAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state) {
            $this->_state = self::STATE_OFF;
            $this->_offset = -1;
            $this->_recordCount = 0;
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Przechodzi do pierwszego rekordu
     * 
     * @param array $params
     * @param bool $compositePart 
     * @return array
     */
    public function firstAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state && $this->_recordCount) {
            $this->_offset = 0;
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Przechodzi do ostatniego rekordu
     * 
     * @param array $params
     * @param bool $compositePart 
     * @return array
     */
    public function lastAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state) {
            $this->_offset = $this->_recordCount - 1;
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Przechodzi do poprzedniego rekordu
     * 
     * @param array $params
     * @param bool $compositePart 
     * @return array
     */
    public function previousAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state)
            if ($this->_offset > 0) {
                $this->_offset--;
                if (!$compositePart) {
                    $this->_setActionState($params);
                }
            }
        return $result;
    }

    /**
     * Przechodzi do nastepnego rekordu
     * 
     * @param array $params
     * @param bool $compositePart 
     * @return array
     */
    public function nextAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state)
            if ($this->_offset < $this->_recordCount - 1) {
                $this->_offset++;
                if (!$compositePart) {
                    $this->_setActionState($params);
                }
            }
        return $result;
    }

    /**
     * Przechodzi do rekordu o podanej pozycji (ofsecie)
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function seekAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if (isset($params['offset'])) {
            if ($this->_state > 0) {
                if (($params['offset'] < $this->_recordCount) && ($params['offset'] >= 0)) {
                    $this->_offset = $params['offset'];
                    if (!$compositePart) {
                        $this->_setActionState($params);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Przechodzi do pierwszego rekordu pasującego do podanych kryteriów
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function searchAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if (isset($params['searchValues'])) {
            $array = $this->getItems();
            if (!is_array($array)) {
                $array = $array->toArray();
            }
            foreach ($array as $key => $value) {
                $bool = true;
                foreach ($params['searchValues'] as $field => $fieldValue) {
                    if (is_array($fieldValue)) {
                        $searched = $fieldValue['value'];
                        $operator = $fieldValue['equalization'];
                    } else {
                        $searched = $fieldValue;
                        $operator = self::OPERATOR_EQUAL;
                    }
                    if ($operator == self::OPERATOR_EQUAL)
                        $bool = $bool && ($value[$field] == $searched);
                    else
                        $bool = $bool && (strpos($value[$field], $searched) !== false);
                }
                if ($bool) {
                    $result = array_merge($result, $this->seekAction(array('offset' => $key), true));
                    if (!$compositePart) {
                        $this->_setActionState($params);
                    }
                    return $result;
                }
            }
        }
        return $result;
    }

    /**
     * Pusta akcja odświeżająca dane
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function refreshAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        $this->_state = self::STATE_VIEW;
        if (!$compositePart) {
            $this->_setActionState($params);
        }
        return $result;
    }

    /**
     * Filtruje zbiór po podanych kryteriach
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function filterAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if (isset($params['filter'])) {
            $result = $this->closeAction(null, true);
            if ($params['filter'] instanceof Filter) {
                $this->_filter = $params['filter'];
            } else {
                $this->_filter->setFilters($params['filter']);
            }
            $result = array_merge($result, $this->openAction(array('first' => true), true));
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Akcja usuwająca filtry
     * 
     * @param type $params
     * @param type $compositePart
     * @return array
     */
    public function clearfilterAction($params = array(), $compositePart = false) {
        $result = array();
        $this->closeAction(array(), true);
        $this->_filter->clearFilters();
        $this->openAction(array('first' => true), true);
        if (!$compositePart) {
            $this->_setActionState($params);
        }
        return $result;
    }

    /**
     * Filtruje zbiór i wyszukuje pierwszy rekord pasujący do podanych kryteriów
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function filterSearchAction($params = array(), $compositePart = false) {
        $result = array();
        if (isset($params['filter']) && isset($params['searchValues'])) {
            $result = array_merge($result, $this->filterAction(array('filter' => $params['filter']), true));
            $result = array_merge($result, $this->searchAction(array('searchValues' => $params['searchValues']), true));
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Filtruje zbiór i przechodzi do podanego offsetu
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function filterSeekAction($params = array(), $compositePart = false) {
        $result = array();
        if (isset($params['filter']) && isset($params['offset'])) {
            $result = array_merge($result, $this->filterAction(array('filter' => $params['filter']), true));
            if (isset($params['offset']))
                $result = array_merge($result, $this->seekAction(array('offset' => $params['offset']), true));
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Sortuje zbiór po podanej kolumnie i w podanym kierunku
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function sortAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if (isset($params['field'])) {
            if (is_array($params['field'])) {
                foreach ($params['field'] as $field) {
                    $direction = 'asc';
                    if (is_array($field)) {
                        $field = $field[0];
                        $direction = $field[1];
                    }
                    $this->_order->setSort(array('field' => $field, 'direction' => $direction));
                }
            } else {
                if (!isset($params['direction']))
                    $params['direction'] = 'asc';
                if ($params['direction'] == 'clear') {
                    $this->_order->removeSort($params['field']);
                } else {
                    $this->_order->setSort(array('field' => $params['field'], 'direction' => $params['direction']));
                }
            }
        }
        return $result;
    }

    /**
     * Przechodzi do pierwszej strony rekordów
     * 
     * @param array $params
     * @param bool $compositePart 
     * @return array
     */
    public function firstPageAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state && $this->_recordCount) {
            $this->_page = 1;
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Przechodzi do ostatniej strony rekordów
     * 
     * @param array $params
     * @param bool $compositePart 
     * @return array
     */
    public function lastPageAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state && $this->_recordCount && $this->_recordPerPage) {
            $this->_page = ceil($this->_recordCount / $this->_recordPerPage);
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Przechodzi do poprzedniej strony rekordów
     * 
     * @param array $params
     * @param bool $compositePart 
     * @return array
     */
    public function previousPageAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state && $this->_page > 1) {
            $this->_page--;
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Przechodzi do nastepnej strony rekordów
     * 
     * @param array $params
     * @param bool $compositePart 
     * @return array
     */
    public function nextPageAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if ($this->_state && $this->_recordPerPage && $this->_page < $this->getPageCount()) {
            $this->_page++;
            if (!$compositePart) {
                $this->_setActionState($params);
            }
        }
        return $result;
    }

    /**
     * Przechodzi do strony o podanym numerze
     * 
     * @param array $params
     * @param bool $compositePart
     * @return array
     */
    public function seekPageAction($params = array(), $compositePart = false) {
        Msg::add($this->getId() . '->' . __FUNCTION__);
        $result = array();
        if (isset($params['page'])) {
            if ($this->_state > 0) {
                //jeśli jest w stanie dodawania rekordu, przejdzie do stanu przeglądu
                $this->_state = self::STATE_VIEW;
                if (($params['page'] <= $this->getPageCount()) && ($params['page'] > 0)) {
                    $this->_page = $params['page'];
                    if (!$compositePart) {
                        $this->_setActionState($params);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Eksportuje dane zbioru do arkusza w formacie MS Excel
     * 
     * @param array $params
     * @param bool $compositePart
     * @return void
     */
    public function exportExcelAction($params = array(), $compositePart = false) {
        $xls = new \PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        $sheet->setTitle('Records');
        //nagłówek arkusza
        if (isset($params['fields'])) {
            //parametry są przekazywane w adresie, dlatego tablice trzeba obsłużyć ręcznie
            $fields = explode(',', $params['fields']);
        } else {
            $fields = $this->getColumns();
        }
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
                'endcolor' => array(
                    'argb' => 'FFFFFFFF',
                ),
            ),
        );
        $sheet->getStyle('1')->applyFromArray($styleArray);
        $sheet->fromArray($fields, null, 'A1');
        for ($i = 0; $i < count($fields); $i++)
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        //rekordy
        $rows = $this->getItems(null, null, $fields);
        $sheet->fromArray($rows, null, 'A2');

        header('Content-Type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s.xlsx"', $this->getId()));
        $writer = new \PHPExcel_Writer_Excel2007($xls);
        $writer->save('php://output');
        exit;
    }

    /**
     * Generuje raport do druku
     * 
     * @param array $params
     * @param bool $compositePart
     * @return void
     */
    public function printAction($params = array(), $compositePart = false) {
        $dataSource = new DataSource($this->getId() . 'Source', $this);
        $report = new PrintDataSet('DataSetReport', array('dataSource' => $dataSource));
        echo $report->render();
        exit;
    }

}
