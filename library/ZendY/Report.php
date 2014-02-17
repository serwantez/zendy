<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

use ZendY\Component;
use ZendY\Exception;
use ZendY\Report\Decorator\BaseInterface;
use ZendY\Db\DataSource;
use ZendY\Report\Element;
use ZendY\Db\DataInterface;

/**
 * Klasa raportu
 *
 * @author Piotr Zając
 */
class Report extends Component implements \Iterator, \Countable, DataInterface {

    const ELEMENT = 'ELEMENT';

    /**
     * Atrybuty i metadane raportu
     * 
     * @var array
     */
    protected $_attribs = array();

    /**
     * Źródło danych
     * 
     * @var \ZendY\Db\DataSource
     */
    protected $_dataSource;

    /**
     * Opis raportu
     * 
     * @var string
     */
    protected $_description;

    /**
     * Global decorators to apply to all elements
     * 
     * @var null|array
     */
    protected $_elementDecorators;

    /**
     * Prefix paths to use when creating elements
     * 
     * @var array
     */
    protected $_elementPrefixPaths = array();

    /**
     * Elementy raportu
     * 
     * @var array
     */
    protected $_elements = array();

    /**
     * Array to which elements belong (if any)
     * 
     * @var string
     */
    protected $_elementsBelongTo;

    /**
     * Whether or not form elements are members of an array
     * 
     * @var bool
     */
    protected $_isArray = false;

    /**
     * @var bool
     */
    protected $_isRendered = false;

    /**
     * Porządek wyświetlania i iteracji elementów
     * 
     * @var array
     */
    protected $_order = array();

    /**
     * Whether internal order has been updated or not
     * 
     * @var bool
     */
    protected $_orderUpdated = false;

    /**
     * Report order
     * 
     * @var int|null
     */
    protected $_reportOrder;

    /**
     * Sub report prefix paths
     * 
     * @var array
     */
    protected $_subReportPrefixPaths = array();

    /**
     * Sub reports
     * 
     * @var array
     */
    protected $_subReports = array();

    /**
     * Konstruktor
     * 
     * @param string|null $id
     * @param array|Zend_Config|null $options
     * @return void
     */
    public function __construct($options = null) {
        $this->addPrefixPath('ZendY\\Report\\Decorator', 'ZendY/Report/Decorator', self::DECORATOR);
        $this->setAttrib('class', 'ui-report');
        parent::__construct($options);
    }

    /**
     * Clone report object and all children
     *
     * @return void
     */
    public function __clone() {
        $elements = array();
        foreach ($this->getElements() as $name => $element) {
            $elements[] = clone $element;
        }
        $this->setElements($elements);

        $subReports = array();
        foreach ($this->getSubReports() as $name => $subReport) {
            $subReports[$name] = clone $subReport;
        }
        $this->setSubReports($subReports);
    }

    /**
     * Ustawia źródło danych
     * 
     * @param \ZendY\Db\DataSource|null $dataSource
     * @return \ZendY\Report
     */
    public function setDataSource(&$dataSource) {
        if ($dataSource instanceof DataSource) {
            $this->_dataSource = $dataSource;
        } else {
            throw new Exception('Instance of ZendY\Report only accepts instances of the type ZendY\Db\DataSource');
        }

        return $this;
    }

    /**
     * Zwraca źródło danych
     * 
     * @return \ZendY\Db\DataSource 
     */
    public function getDataSource() {
        return $this->_dataSource;
    }

    /**
     * Set report state from options array
     * 
     * @param array $options
     * @return \ZendY\Report
     */
    public function setOptions(array $options) {
        if (isset($options['prefixPath'])) {
            $this->addPrefixPaths($options['prefixPath']);
            unset($options['prefixPath']);
        }

        if (isset($options['elementPrefixPath'])) {
            $this->addElementPrefixPaths($options['elementPrefixPath']);
            unset($options['elementPrefixPath']);
        }

        if (isset($options['elementDecorators'])) {
            $this->_elementDecorators = $options['elementDecorators'];
            unset($options['elementDecorators']);
        }

        if (isset($options['elements'])) {
            $this->setElements($options['elements']);
            unset($options['elements']);
        }

        if (isset($options['elementsBelongTo'])) {
            $elementsBelongTo = $options['elementsBelongTo'];
            unset($options['elementsBelongTo']);
        }

        if (isset($options['attribs'])) {
            $this->addAttribs($options['attribs']);
            unset($options['attribs']);
        }

        $forbidden = array(
            'Options', 'Config', 'PluginLoader', 'SubReports', 'Translator',
            'Attrib', 'Default',
        );

        foreach ($options as $key => $value) {
            $normalized = ucfirst($key);
            if (in_array($normalized, $forbidden)) {
                continue;
            }

            $method = 'set' . $normalized;
            if (method_exists($this, $method)) {
                if ($normalized == 'View' && !($value instanceof \Zend_View_Interface)) {
                    continue;
                }
                $this->$method($value);
            } else {
                $this->setAttrib($key, $value);
            }
        }

        if (isset($elementsBelongTo)) {
            $this->setElementsBelongTo($elementsBelongTo);
        }

        return $this;
    }

    /**
     * Set plugin loaders for use with decorators and elements
     *
     * @param  \Zend_Loader_PluginLoader_Interface $loader
     * @param  string $type 'decorator' or 'element'
     * @return \ZendY\Report
     * @throws Exception on invalid type
     */
    public function setPluginLoader(\Zend_Loader_PluginLoader_Interface $loader, $type = null) {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
            case self::ELEMENT:
                $this->_loaders[$type] = $loader;
                return $this;
            default:
                throw new Exception(sprintf('Invalid type "%s" provided to setPluginLoader()', $type));
        }
    }

    /**
     * Retrieve plugin loader for given type
     *
     * $type may be one of:
     * - decorator
     * - element
     *
     * If a plugin loader does not exist for the given type, defaults are
     * created.
     *
     * @param  string $type
     * @return \Zend_Loader_PluginLoader_Interface
     */
    public function getPluginLoader($type = null) {
        $type = strtoupper($type);
        if (!isset($this->_loaders[$type])) {
            switch ($type) {
                case self::DECORATOR:
                    $prefixSegment = 'Report\\Decorator';
                    $pathSegment = 'Report/Decorator';
                    break;
                case self::ELEMENT:
                    $prefixSegment = 'Report\\Element';
                    $pathSegment = 'Report/Element';
                    break;
                default:
                    throw new Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
            }

            require_once 'Zend/Loader/PluginLoader.php';
            $this->_loaders[$type] = new \Zend_Loader_PluginLoader(
                            array('ZendY\\' . $prefixSegment . '\\' => 'ZendY/' . $pathSegment . '/')
            );
        }

        return $this->_loaders[$type];
    }

    /**
     * Add prefix path for plugin loader
     *
     * @param  string $prefix
     * @param  string $path
     * @param  string $type
     * @return \ZendY\Report
     * @throws Exception for invalid type
     */
    public function addPrefixPath($prefix, $path, $type = null) {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
            case self::ELEMENT:
                $loader = $this->getPluginLoader($type);
                $loader->addPrefixPath($prefix, $path);
                return $this;
            case null:
                $nsSeparator = (false !== strpos($prefix, '\\')) ? '\\' : '_';
                $prefix = rtrim($prefix, $nsSeparator);
                $path = rtrim($path, DIRECTORY_SEPARATOR);
                foreach (array(self::DECORATOR, self::ELEMENT) as $type) {
                    $cType = ucfirst(strtolower($type));
                    $pluginPath = $path . DIRECTORY_SEPARATOR . $cType . DIRECTORY_SEPARATOR;
                    $pluginPrefix = $prefix . $nsSeparator . $cType;
                    $loader = $this->getPluginLoader($type);
                    $loader->addPrefixPath($pluginPrefix, $pluginPath);
                }
                return $this;
            default:
                throw new Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Add prefix path for all elements
     *
     * @param  string $prefix
     * @param  string $path
     * @param  string $type
     * @return \ZendY\Report
     */
    public function addElementPrefixPath($prefix, $path, $type = null) {
        $this->_elementPrefixPaths[] = array(
            'prefix' => $prefix,
            'path' => $path,
            'type' => $type,
        );

        foreach ($this->getElements() as $element) {
            $element->addPrefixPath($prefix, $path, $type);
        }

        foreach ($this->getSubReports() as $subForm) {
            $subForm->addElementPrefixPath($prefix, $path, $type);
        }

        return $this;
    }

    /**
     * Add prefix paths for all elements
     *
     * @param  array $spec
     * @return \ZendY\Report
     */
    public function addElementPrefixPaths(array $spec) {
        $this->_elementPrefixPaths = $this->_elementPrefixPaths + $spec;

        foreach ($this->getElements() as $element) {
            $element->addPrefixPaths($spec);
        }

        return $this;
    }

    /**
     * Set report attribute
     *
     * @param  string $key
     * @param  mixed $value
     * @return \ZendY\Report
     */
    public function setAttrib($key, $value) {
        $key = (string) $key;
        $this->_attribs[$key] = $value;
        return $this;
    }

    /**
     * Add multiple report attributes at once
     *
     * @param  array $attribs
     * @return \ZendY\Report
     */
    public function addAttribs(array $attribs) {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }
        return $this;
    }

    /**
     * Set multiple report attributes at once
     *
     * Overwrites any previously set attributes.
     *
     * @param  array $attribs
     * @return \ZendY\Report
     */
    public function setAttribs(array $attribs) {
        $this->clearAttribs();
        return $this->addAttribs($attribs);
    }

    /**
     * Retrieve a single report attribute
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttrib($key) {
        $key = (string) $key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
    }

    /**
     * Retrieve all report attributes/metadata
     *
     * @return array
     */
    public function getAttribs() {
        return $this->_attribs;
    }

    /**
     * Remove attribute
     *
     * @param  string $key
     * @return bool
     */
    public function removeAttrib($key) {
        if (isset($this->_attribs[$key])) {
            unset($this->_attribs[$key]);
            return true;
        }

        return false;
    }

    /**
     * Clear all report attributes
     * 
     * @return \ZendY\Report
     */
    public function clearAttribs() {
        $this->_attribs = array();
        return $this;
    }

    /**
     * Zwraca nazwę raportu
     *
     * @return null|string
     */
    public function getName() {
        return $this->getAttrib('name');
    }

    /**
     * Get fully qualified name
     *
     * Places name as subitem of array and/or appends brackets.
     *
     * @return string
     */
    public function getFullyQualifiedName() {
        return $this->getName();
    }

    /**
     * Set report description
     *
     * @param  string $value
     * @return \ZendY\Report
     */
    public function setDescription($value) {
        $this->_description = (string) $value;
        return $this;
    }

    /**
     * Retrieve report description
     *
     * @return string
     */
    public function getDescription() {
        return $this->_description;
    }

    /**
     * Set form order
     *
     * @param  int $index
     * @return \ZendY\Report
     */
    public function setOrder($index) {
        $this->_reportOrder = (int) $index;
        return $this;
    }

    /**
     * Get report order
     *
     * @return int|null
     */
    public function getOrder() {
        return $this->_reportOrder;
    }

    /**
     * When calling renderReportElements or render this method
     * is used to set $_isRendered member to prevent repeatedly
     * merging belongsTo setting
     * 
     * @return \ZendY\Report
     */
    protected function _setIsRendered() {
        $this->_isRendered = true;
        return $this;
    }

    /**
     * Get the value of $_isRendered member
     * 
     * @return bool
     */
    protected function _getIsRendered() {
        return (bool) $this->_isRendered;
    }

    /**
     * Dodaje nowy element do raportu
     *
     * @param  string|\ZendY\Report\Element $element
     * @param  string $name
     * @param  array|\Zend_Config $options
     * @throws Exception on invalid element
     * @return \ZendY\Report
     */
    public function addElement($element, $name = null, $options = null) {
        if (is_string($element)) {
            if (null === $name) {
                throw new Exception('Elements specified by string must have an accompanying name');
            }

            if (is_array($this->_elementDecorators)) {
                if (null === $options) {
                    $options = array('decorators' => $this->_elementDecorators);
                } elseif ($options instanceof \Zend_Config) {
                    $options = $options->toArray();
                }
                if (is_array($options)
                        && !array_key_exists('decorators', $options)
                ) {
                    $options['decorators'] = $this->_elementDecorators;
                }
            }

            $this->_elements[$name] = $this->createElement($element, $name, $options);
        } elseif ($element instanceof Element) {
            $prefixPaths = array();
            $prefixPaths['decorator'] = $this->getPluginLoader('decorator')->getPaths();
            if (!empty($this->_elementPrefixPaths)) {
                $prefixPaths = array_merge($prefixPaths, $this->_elementPrefixPaths);
            }

            if (null === $name) {
                $name = $element->getName();
            }

            if (isset($this->_dataSource) && $element instanceof DataInterface) {
                $element->setDataSource($this->_dataSource);
            }
            $this->_elements[$name] = $element;
            $this->_elements[$name]->addPrefixPaths($prefixPaths);
        } else {
            throw new Exception('Element must be specified by string or ZendY_Report_Element instance');
        }

        $this->_order[$name] = $this->_elements[$name]->getOrder();
        $this->_orderUpdated = true;
        $this->_setElementsBelongTo($name);

        return $this;
    }

    /**
     * Tworzy element raportu
     * 
     * @param string $type
     * @param string $name
     * @param array|\Zend_Config $options
     * @return \ZendY\Form\Element
     * @throws Exception
     */
    public function createElement($type, $name, $options = null) {
        if (!is_string($type)) {
            throw new Exception('Element type must be a string indicating type');
        }

        if (!is_string($name)) {
            throw new Exception('Element name must be a string');
        }

        $prefixPaths = array();
        $prefixPaths['decorator'] = $this->getPluginLoader('decorator')->getPaths();
        if (!empty($this->_elementPrefixPaths)) {
            $prefixPaths = array_merge($prefixPaths, $this->_elementPrefixPaths);
        }

        if ($options instanceof \Zend_Config) {
            $options = $options->toArray();
        }

        if ((null === $options) || !is_array($options)) {
            $options = array('prefixPath' => $prefixPaths);
        } elseif (is_array($options)) {
            if (array_key_exists('prefixPath', $options)) {
                $options['prefixPath'] = array_merge($prefixPaths, $options['prefixPath']);
            } else {
                $options['prefixPath'] = $prefixPaths;
            }
        }

        $class = $this->getPluginLoader(self::ELEMENT)->load($type);
        $element = new $class($name, $options);

        return $element;
    }

    /**
     * Dodaje wiele elementów na raz
     *
     * @param  array $elements
     * @return \ZendY\Report
     */
    public function addElements(array $elements) {
        foreach ($elements as $key => $spec) {
            $name = null;
            if (!is_numeric($key)) {
                $name = $key;
            }

            if (is_string($spec) || ($spec instanceof Element)) {
                $this->addElement($spec, $name);
                continue;
            }

            if (is_array($spec)) {
                $argc = count($spec);
                $options = array();
                if (isset($spec['type'])) {
                    $type = $spec['type'];
                    if (isset($spec['name'])) {
                        $name = $spec['name'];
                    }
                    if (isset($spec['options'])) {
                        $options = $spec['options'];
                    }
                    $this->addElement($type, $name, $options);
                } else {
                    switch ($argc) {
                        case 0:
                            continue;
                        case (1 <= $argc):
                            $type = array_shift($spec);
                        case (2 <= $argc):
                            if (null === $name) {
                                $name = array_shift($spec);
                            } else {
                                $options = array_shift($spec);
                            }
                        case (3 <= $argc):
                            if (empty($options)) {
                                $options = array_shift($spec);
                            }
                        default:
                            $this->addElement($type, $name, $options);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Set report elements (overwrites existing elements)
     *
     * @param  array $elements
     * @return \ZendY\Report
     */
    public function setElements(array $elements) {
        $this->clearElements();
        return $this->addElements($elements);
    }

    /**
     * Retrieve a single element
     *
     * @param  string $name
     * @return \ZendY\Report\Element|null
     */
    public function getElement($name) {
        if (array_key_exists($name, $this->_elements)) {
            return $this->_elements[$name];
        }
        return null;
    }

    /**
     * Retrieve all elements
     *
     * @return array
     */
    public function getElements() {
        return $this->_elements;
    }

    /**
     * Remove element
     *
     * @param  string $name
     * @return bool
     */
    public function removeElement($name) {
        $name = (string) $name;
        if (isset($this->_elements[$name])) {
            unset($this->_elements[$name]);
            if (array_key_exists($name, $this->_order)) {
                unset($this->_order[$name]);
                $this->_orderUpdated = true;
            }
            return true;
        }
        return false;
    }

    /**
     * Remove all report elements
     *
     * @return \ZendY\Report
     */
    public function clearElements() {
        foreach (array_keys($this->_elements) as $key) {
            if (array_key_exists($key, $this->_order)) {
                unset($this->_order[$key]);
            }
        }
        $this->_elements = array();
        $this->_orderUpdated = true;
        return $this;
    }

    /**
     * Set name of array elements belong to
     *
     * @param  string $array
     * @return \ZendY\Report
     */
    public function setElementsBelongTo($array) {
        $origName = $this->getElementsBelongTo();
        $name = $this->filterName($array, true);
        if ('' === $name) {
            $name = null;
        }
        $this->_elementsBelongTo = $name;

        if (null === $name) {
            $this->setIsArray(false);
            if (null !== $origName) {
                $this->_setElementsBelongTo();
            }
        } else {
            $this->setIsArray(true);
            $this->_setElementsBelongTo();
        }
        return $this;
    }

    /**
     * Set array to which elements belong
     *
     * @param  string $name Element name
     * @return void
     */
    protected function _setElementsBelongTo($name = null) {
        $array = $this->getElementsBelongTo();

        if (null === $array) {
            return;
        }

        if (null === $name) {
            foreach ($this->getElements() as $element) {
                $element->setBelongsTo($array);
            }
        } else {
            if (null !== ($element = $this->getElement($name))) {
                $element->setBelongsTo($array);
            }
        }
    }

    /**
     * Get name of array elements belong to
     *
     * @return string|null
     */
    public function getElementsBelongTo() {
        if ((null === $this->_elementsBelongTo) && $this->isArray()) {
            $name = $this->getName();
            if ('' !== (string) $name) {
                return $name;
            }
        }
        return $this->_elementsBelongTo;
    }

    /**
     * Set flag indicating elements belong to array
     *
     * @param  bool $flag Value of flag
     * @return \ZendY\Report
     */
    public function setIsArray($flag) {
        $this->_isArray = (bool) $flag;
        return $this;
    }

    /**
     * Get flag indicating if elements belong to an array
     *
     * @return bool
     */
    public function isArray() {
        return $this->_isArray;
    }

    // Element groups:

    /**
     * Add a subreport
     *
     * @param  \ZendY\Report $form
     * @param  string $name
     * @param  int $order
     * @return \ZendY\Report
     */
    public function addSubReport(Report $report, $name, $order = null) {
        $name = (string) $name;
        foreach ($this->_loaders as $type => $loader) {
            $loaderPaths = $loader->getPaths();
            foreach ($loaderPaths as $prefix => $paths) {
                foreach ($paths as $path) {
                    $report->addPrefixPath($prefix, $path, $type);
                }
            }
        }

        if (!empty($this->_elementPrefixPaths)) {
            foreach ($this->_elementPrefixPaths as $spec) {
                list($prefix, $path, $type) = array_values($spec);
                $report->addElementPrefixPath($prefix, $path, $type);
            }
        }

        if (null !== $order) {
            $report->setOrder($order);
        }

        if (($oldName = $report->getName()) &&
                $oldName !== $name &&
                $oldName === $report->getElementsBelongTo()) {
            $report->setElementsBelongTo($name);
        }

        $report->setName($name);
        $this->_subReports[$name] = $report;
        $this->_order[$name] = $order;
        $this->_orderUpdated = true;
        return $this;
    }

    /**
     * Add multiple subreports at once
     *
     * @param  array $subReports
     * @return \ZendY\Report
     */
    public function addSubReports(array $subReports) {
        foreach ($subReports as $key => $spec) {
            $name = (string) $key;
            if ($spec instanceof Report) {
                $this->addSubReport($spec, $name);
                continue;
            }

            if (is_array($spec)) {
                $argc = count($spec);
                $order = null;
                switch ($argc) {
                    case 0:
                        continue;
                    case (1 <= $argc):
                        $subReport = array_shift($spec);
                    case (2 <= $argc):
                        $name = array_shift($spec);
                    case (3 <= $argc):
                        $order = array_shift($spec);
                    default:
                        $this->addSubReport($subReport, $name, $order);
                }
            }
        }
        return $this;
    }

    /**
     * Set multiple subreports (overwrites)
     *
     * @param  array $subReports
     * @return \ZendY\Report
     */
    public function setSubReports(array $subReports) {
        $this->clearSubReports();
        return $this->addSubReports($subReports);
    }

    /**
     * Retrieve a subreport
     *
     * @param  string $name
     * @return \ZendY\Report|null
     */
    public function getSubReport($name) {
        $name = (string) $name;
        if (isset($this->_subReports[$name])) {
            return $this->_subReports[$name];
        }
        return null;
    }

    /**
     * Retrieve all subReports
     *
     * @return array
     */
    public function getSubReports() {
        return $this->_subReports;
    }

    /**
     * Remove subReport
     *
     * @param  string $name
     * @return bool
     */
    public function removeSubReport($name) {
        $name = (string) $name;
        if (array_key_exists($name, $this->_subReports)) {
            unset($this->_subReports[$name]);
            if (array_key_exists($name, $this->_order)) {
                unset($this->_order[$name]);
                $this->_orderUpdated = true;
            }
            return true;
        }

        return false;
    }

    /**
     * Remove all subReports
     *
     * @return \ZendY\Report
     */
    public function clearSubReports() {
        foreach (array_keys($this->_subReports) as $key) {
            if (array_key_exists($key, $this->_order)) {
                unset($this->_order[$key]);
            }
        }
        $this->_subReports = array();
        $this->_orderUpdated = true;
        return $this;
    }

    // Rendering

    /**
     * Set view object
     *
     * @param  \Zend_View_Interface $view
     * @return \ZendY\Report
     */
    public function setView(\Zend_View_Interface $view = null) {
        $this->_view = $view;
        return $this;
    }

    /**
     * Add a decorator for rendering the element
     *
     * @param  string|\ZendY\Report\Decorator\BaseInterface $decorator
     * @param  array|\Zend_Config $options Options with which to initialize decorator
     * @return \ZendY\Report
     */
    public function addDecorator($decorator, $options = null) {
        if ($decorator instanceof BaseInterface) {
            $name = get_class($decorator);
        } elseif (is_string($decorator)) {
            $name = $decorator;
            $decorator = array(
                'decorator' => $name,
                'options' => $options,
            );
        } elseif (is_array($decorator)) {
            foreach ($decorator as $name => $spec) {
                break;
            }
            if (is_numeric($name)) {
                throw new Exception('Invalid alias provided to addDecorator; must be alphanumeric string');
            }
            if (is_string($spec)) {
                $decorator = array(
                    'decorator' => $spec,
                    'options' => $options,
                );
            } elseif ($spec instanceof BaseInterface) {
                $decorator = $spec;
            }
        } else {
            throw new Exception('Invalid decorator provided to addDecorator; must be string or ZendY_Report_Decorator_Interface');
        }

        $this->_decorators[$name] = $decorator;

        return $this;
    }

    /**
     * Add many decorators at once
     *
     * @param  array $decorators
     * @return \ZendY\Report
     */
    public function addDecorators(array $decorators) {
        foreach ($decorators as $decoratorName => $decoratorInfo) {
            if (is_string($decoratorInfo) ||
                    $decoratorInfo instanceof BaseInterface) {
                if (!is_numeric($decoratorName)) {
                    $this->addDecorator(array($decoratorName => $decoratorInfo));
                } else {
                    $this->addDecorator($decoratorInfo);
                }
            } elseif (is_array($decoratorInfo)) {
                $argc = count($decoratorInfo);
                $options = array();
                if (isset($decoratorInfo['decorator'])) {
                    $decorator = $decoratorInfo['decorator'];
                    if (isset($decoratorInfo['options'])) {
                        $options = $decoratorInfo['options'];
                    }
                    $this->addDecorator($decorator, $options);
                } else {
                    switch (true) {
                        case (0 == $argc):
                            break;
                        case (1 <= $argc):
                            $decorator = array_shift($decoratorInfo);
                        case (2 <= $argc):
                            $options = array_shift($decoratorInfo);
                        default:
                            $this->addDecorator($decorator, $options);
                            break;
                    }
                }
            } else {
                throw new Exception('Invalid decorator passed to addDecorators()');
            }
        }

        return $this;
    }

    /**
     * Set all element decorators as specified
     *
     * @param  array $decorators
     * @param  array|null $elements Specific elements to decorate or exclude from decoration
     * @param  bool $include Whether $elements is an inclusion or exclusion list
     * @return \ZendY\Report
     */
    public function setElementDecorators(array $decorators, array $elements = null, $include = true) {
        if (is_array($elements)) {
            if ($include) {
                $elementObjs = array();
                foreach ($elements as $name) {
                    if (null !== ($element = $this->getElement($name))) {
                        $elementObjs[] = $element;
                    }
                }
            } else {
                $elementObjs = $this->getElements();
                foreach ($elements as $name) {
                    if (array_key_exists($name, $elementObjs)) {
                        unset($elementObjs[$name]);
                    }
                }
            }
        } else {
            $elementObjs = $this->getElements();
        }

        foreach ($elementObjs as $element) {
            $element->setDecorators($decorators);
        }

        $this->_elementDecorators = $decorators;

        return $this;
    }

    /**
     * Set all subreport decorators as specified
     *
     * @param  array $decorators
     * @return \ZendY\Report
     */
    public function setSubReportDecorators(array $decorators) {
        foreach ($this->getSubReports() as $form) {
            $form->setDecorators($decorators);
        }

        return $this;
    }

    /**
     * Load the default decorators
     *
     * @return \ZendY\Report
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ReportElements')
                    ->addDecorator('Report');
        }
        return $this;
    }

    /**
     * Renderuje raport
     *
     * @param  \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if (null !== $view) {
            $this->setView($view);
        }

        $content = '';
        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
        $this->_setIsRendered();
        return $content;
    }

    /**
     * Serialize as string
     *
     * Proxies to {@link render()}.
     *
     * @return string
     */
    public function __toString() {
        try {
            $return = $this->render();
            return $return;
        } catch (Exception $e) {
            $message = "Exception caught by report: " . $e->getMessage()
                    . "\nStack Trace:\n" . $e->getTraceAsString();
            trigger_error($message, E_USER_WARNING);
            return '';
        }
    }

    /**
     * Overloading: access to elements, report groups
     *
     * @param  string $name
     * @return \ZendY\Report\Element|\ZendY\Report|null
     */
    public function __get($name) {
        if (isset($this->_elements[$name])) {
            return $this->_elements[$name];
        } elseif (isset($this->_subReports[$name])) {
            return $this->_subReports[$name];
        }

        return null;
    }

    /**
     * Overloading: access to elements, report groups
     *
     * @param  string $name
     * @param  \ZendY\Report\Element|\ZendY\Report $value
     * @return void
     * @throws Exception for invalid $value
     */
    public function __set($name, $value) {
        if ($value instanceof Element) {
            $this->addElement($value, $name);
            return;
        } elseif ($value instanceof Report) {
            $this->addSubReport($value, $name);
            return;
        }

        if (is_object($value)) {
            $type = get_class($value);
        } else {
            $type = gettype($value);
        }
        throw new Exception('Only report elements and groups may be overloaded; variable of type "' . $type . '" provided');
    }

    /**
     * Overloading: access to elements, report groups
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name) {
        if (isset($this->_elements[$name])
                || isset($this->_subReports[$name])) {
            return true;
        }

        return false;
    }

    /**
     * Overloading: access to elements, report groups
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name) {
        if (isset($this->_elements[$name])) {
            unset($this->_elements[$name]);
        } elseif (isset($this->_subReports[$name])) {
            unset($this->_subReports[$name]);
        }
    }

    /**
     * Overloading: allow rendering specific decorators
     *
     * Call renderDecoratorName() to render a specific decorator.
     *
     * @param  string $method
     * @param  array $args
     * @return string
     * @throws Exception for invalid decorator or invalid method call
     */
    public function __call($method, $args) {
        if ('render' == substr($method, 0, 6)) {
            $decoratorName = substr($method, 6);
            if (false !== ($decorator = $this->getDecorator($decoratorName))) {
                $decorator->setElement($this);
                $seed = '';
                if (0 < count($args)) {
                    $seed = array_shift($args);
                }
                if ($decoratorName === 'ReportElements' ||
                        $decoratorName === 'PrepareElements') {
                    $this->_setIsRendered();
                }
                return $decorator->render($seed);
            }

            throw new Exception(sprintf('Decorator by name %s does not exist', $decoratorName));
        }

        throw new Exception(sprintf('Method %s does not exist', $method));
    }

    // Interfaces: Iterator, Countable

    /**
     * Current element/subreport
     *
     * @return \ZendY\Report\Element|\ZendY\Report
     */
    public function current() {
        $this->_sort();
        current($this->_order);
        $key = key($this->_order);

        if (isset($this->_elements[$key])) {
            return $this->getElement($key);
        } elseif (isset($this->_subReports[$key])) {
            return $this->getSubReport($key);
        } else {
            throw new Exception(sprintf('Corruption detected in form; invalid key ("%s") found in internal iterator', (string) $key));
        }
    }

    /**
     * Current element/subreport name
     *
     * @return string
     */
    public function key() {
        $this->_sort();
        return key($this->_order);
    }

    /**
     * Move pointer to next element/subreport
     *
     * @return void
     */
    public function next() {
        $this->_sort();
        next($this->_order);
    }

    /**
     * Move pointer to beginning of element/subreport loop
     *
     * @return void
     */
    public function rewind() {
        $this->_sort();
        reset($this->_order);
    }

    /**
     * Determine if current element/subreport is valid
     *
     * @return bool
     */
    public function valid() {
        $this->_sort();
        return (current($this->_order) !== false);
    }

    /**
     * Count of elements/subreports that are iterable
     *
     * @return int
     */
    public function count() {
        return count($this->_order);
    }

    /**
     * Sort items according to their order
     *
     * @return void
     */
    protected function _sort() {
        if ($this->_orderUpdated) {
            $items = array();
            $index = 0;
            foreach ($this->_order as $key => $order) {
                if (null === $order) {
                    if (null === ($order = $this->{$key}->getOrder())) {
                        while (array_search($index, $this->_order, true)) {
                            ++$index;
                        }
                        $items[$index] = $key;
                        ++$index;
                    } else {
                        $items[$order] = $key;
                    }
                } else {
                    $items[$order] = $key;
                }
            }

            $items = array_flip($items);
            asort($items);
            $this->_order = $items;
            $this->_orderUpdated = false;
        }
    }

}