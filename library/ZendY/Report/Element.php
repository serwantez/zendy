<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report;

use ZendY\Exception;
use ZendY\Report;
use ZendY\Report\Decorator\BaseInterface;

/**
 * ZendY_Report_Element
 */
class Element {
    /**
     * Element Constants
     */

    const DECORATOR = 'DECORATOR';

    /**
     * Domyślny pomocnik widoku
     * 
     * @var string
     */
    public $helper = 'reportText';

    /**
     * Array to which element belongs
     * 
     * @var string
     */
    protected $_belongsTo;

    /**
     * Dekoratory elementu
     * 
     * @var array
     */
    protected $_decorators = array();

    /**
     * Opis elementu
     * 
     * @var string
     */
    protected $_description;

    /**
     * Should we disable loading the default decorators?
     * 
     * @var bool
     */
    protected $_disableLoadDefaultDecorators = false;

    /**
     * Ignore flag (used when retrieving values at report level)
     * 
     * @var bool
     */
    protected $_ignore = false;

    /**
     * Does the element represent an array?
     * 
     * @var bool
     */
    protected $_isArray = false;

    /**
     * Element label
     * 
     * @var string
     */
    protected $_label;

    /**
     * Plugin loaders for filter and validator chains
     * 
     * @var array
     */
    protected $_loaders = array();

    /**
     * Element name
     * 
     * @var string
     */
    protected $_name;

    /**
     * Order of element
     * 
     * @var int
     */
    protected $_order;

    /**
     * Translator
     * 
     * @var Zend_Translate
     */
    protected $_translator;

    /**
     * Is translation disabled?
     * 
     * @var bool
     */
    protected $_translatorDisabled = false;

    /**
     * Element type
     * 
     * @var string
     */
    protected $_type;

    /**
     * Element value
     * 
     * @var mixed
     */
    protected $_value;

    /**
     * Widok
     * 
     * @var \Zend_View_Interface
     */
    protected $_view;

    /**
     * Is a specific decorator being rendered via the magic renderDecorator()?
     *
     * This is to allow execution of logic inside the render() methods of child
     * elements during the magic call while skipping the parent render() method.
     *
     * @var bool
     */
    protected $_isPartialRendering = false;

    /**
     * Constructor
     *
     * $spec may be:
     * - string: name of element
     * - array: options with which to configure element
     * - Zend_Config: Zend_Config with options for configuring element
     *
     * @param  string|array|\Zend_Config $spec
     * @param  array|\Zend_Config $options
     * @return void
     * @throws Exception if no element name after initialization
     */
    public function __construct($spec, $options = null) {
        if (is_string($spec)) {
            $this->setName($spec);
        } elseif (is_array($spec)) {
            $this->setOptions($spec);
        } elseif ($spec instanceof \Zend_Config) {
            $this->setConfig($spec);
        }

        if (is_string($spec) && is_array($options)) {
            $this->setOptions($options);
        } elseif (is_string($spec) && ($options instanceof \Zend_Config)) {
            $this->setConfig($options);
        }

        if (null === $this->getName()) {
            throw new Exception('Report element requires each element to have a name');
        }

        /**
         * Extensions
         */
        $this->init();

        /**
         * Register ViewHelper decorator by default
         */
        $this->loadDefaultDecorators();
    }

    /**
     * Initialize object; used by extending classes
     *
     * @return void
     */
    public function init() {
        
    }

    /**
     * Set flag to disable loading default decorators
     *
     * @param  bool $flag
     * @return \ZendY\Report\Element
     */
    public function setDisableLoadDefaultDecorators($flag) {
        $this->_disableLoadDefaultDecorators = (bool) $flag;
        return $this;
    }

    /**
     * Should we load the default decorators?
     *
     * @return bool
     */
    public function loadDefaultDecoratorsIsDisabled() {
        return $this->_disableLoadDefaultDecorators;
    }

    /**
     * Load default decorators
     *
     * @return \ZendY\Report\Element
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper')
                    ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                    ->addDecorator('HtmlTag', array(
                        'tag' => 'dd',
                        'id' => array('callback' => array(get_class($this), 'resolveElementId'))
                    ))
                    ->addDecorator('Label', array('tag' => 'dt'));
        }
        return $this;
    }

    /**
     * Used to resolve and return an element ID
     *
     * Passed to the HtmlTag decorator as a callback in order to provide an ID.
     * 
     * @param  \ZendY_Report\Decorator\Interface $decorator 
     * @return string
     */
    public static function resolveElementId(BaseInterface $decorator) {
        return $decorator->getElement()->getId() . '-element';
    }

    /**
     * Set object state from options array
     *
     * @param  array $options
     * @return \ZendY\Report\Element
     */
    public function setOptions(array $options) {
        if (isset($options['prefixPath'])) {
            $this->addPrefixPaths($options['prefixPath']);
            unset($options['prefixPath']);
        }

        if (isset($options['disableTranslator'])) {
            $this->setDisableTranslator($options['disableTranslator']);
            unset($options['disableTranslator']);
        }

        unset($options['options']);
        unset($options['config']);

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (in_array($method, array('setTranslator', 'setPluginLoader', 'setView'))) {
                if (!is_object($value)) {
                    continue;
                }
            }

            if (method_exists($this, $method)) {
                // Setter exists; use it
                $this->$method($value);
            } else {
                // Assume it's metadata
                $this->setAttrib($key, $value);
            }
        }
        return $this;
    }

    /**
     * Set object state from Zend_Config object
     *
     * @param  \Zend_Config $config
     * @return \ZendY\Report\Element
     */
    public function setConfig(\Zend_Config $config) {
        return $this->setOptions($config->toArray());
    }

    // Localization:

    /**
     * Set translator object for localization
     *
     * @param  \Zend_Translate|null $translator
     * @return \ZendY\Report\Element
     */
    public function setTranslator($translator = null) {
        if (null === $translator) {
            $this->_translator = null;
        } elseif ($translator instanceof \Zend_Translate_Adapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof \Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        } else {
            throw new Exception('Invalid translator specified');
        }
        return $this;
    }

    /**
     * Retrieve localization translator object
     *
     * @return \Zend_Translate_Adapter|null
     */
    public function getTranslator() {
        if ($this->translatorIsDisabled()) {
            return null;
        }

        if (null === $this->_translator) {
            return Report::getDefaultTranslator();
        }
        return $this->_translator;
    }

    /**
     * Does this element have its own specific translator?
     *
     * @return bool
     */
    public function hasTranslator() {
        return (bool) $this->_translator;
    }

    /**
     * Indicate whether or not translation should be disabled
     *
     * @param  bool $flag
     * @return \ZendY\Report\Element
     */
    public function setDisableTranslator($flag) {
        $this->_translatorDisabled = (bool) $flag;
        return $this;
    }

    /**
     * Is translation disabled?
     *
     * @return bool
     */
    public function translatorIsDisabled() {
        return $this->_translatorDisabled;
    }

    // Metadata

    /**
     * Filter a name to only allow valid variable characters
     *
     * @param  string $value
     * @param  bool $allowBrackets
     * @return string
     */
    public function filterName($value, $allowBrackets = false) {
        $charset = '^a-zA-Z0-9_\x7f-\xff';
        if ($allowBrackets) {
            $charset .= '\[\]';
        }
        return preg_replace('/[' . $charset . ']/', '', (string) $value);
    }

    /**
     * Set element name
     *
     * @param  string $name
     * @return \ZendY\Report\Element
     */
    public function setName($name) {
        $name = $this->filterName($name);
        if ('' === $name) {
            throw new Exception('Invalid name provided; must contain only valid variable characters and be non-empty');
        }

        $this->_name = $name;
        return $this;
    }

    /**
     * Return element name
     *
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Get fully qualified name
     *
     * Places name as subitem of array and/or appends brackets.
     *
     * @return string
     */
    public function getFullyQualifiedName() {
        $name = $this->getName();

        if (null !== ($belongsTo = $this->getBelongsTo())) {
            $name = $belongsTo . '[' . $name . ']';
        }

        if ($this->isArray()) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Get element id
     *
     * @return string
     */
    public function getId() {
        if (isset($this->id)) {
            return $this->id;
        }

        $id = $this->getFullyQualifiedName();

        // Bail early if no array notation detected
        if (!strstr($id, '[')) {
            return $id;
        }

        // Strip array notation
        if ('[]' == substr($id, -2)) {
            $id = substr($id, 0, strlen($id) - 2);
        }
        $id = str_replace('][', '-', $id);
        $id = str_replace(array(']', '['), '-', $id);
        $id = trim($id, '-');

        return $id;
    }

    /**
     * Set element value
     *
     * @param  mixed $value
     * @return \ZendY\Report\Element
     */
    public function setValue($value) {
        $this->_value = $value;
        return $this;
    }

    /**
     * Retrieve element value
     *
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * Set element label
     *
     * @param  string $label
     * @return \ZendY\Report\Element
     */
    public function setLabel($label) {
        $this->_label = (string) $label;
        return $this;
    }

    /**
     * Retrieve element label
     *
     * @return string
     */
    public function getLabel() {
        $translator = $this->getTranslator();
        if (null !== $translator) {
            return $translator->translate($this->_label);
        }

        return $this->_label;
    }

    /**
     * Set element order
     *
     * @param  int $order
     * @return \ZendY\Report\Element
     */
    public function setOrder($order) {
        $this->_order = (int) $order;
        return $this;
    }

    /**
     * Retrieve element order
     *
     * @return int
     */
    public function getOrder() {
        return $this->_order;
    }

    /**
     * Set element description
     *
     * @param  string $description
     * @return \ZendY\Report\Element
     */
    public function setDescription($description) {
        $this->_description = (string) $description;
        return $this;
    }

    /**
     * Retrieve element description
     *
     * @return string
     */
    public function getDescription() {
        return $this->_description;
    }

    /**
     * Set ignore flag (used when retrieving values at report level)
     *
     * @param  bool $flag
     * @return \ZendY\Report\Element
     */
    public function setIgnore($flag) {
        $this->_ignore = (bool) $flag;
        return $this;
    }

    /**
     * Get ignore flag (used when retrieving values at report level)
     *
     * @return bool
     */
    public function getIgnore() {
        return $this->_ignore;
    }

    /**
     * Set flag indicating if element represents an array
     *
     * @param  bool $flag
     * @return \ZendY\Report\Element
     */
    public function setIsArray($flag) {
        $this->_isArray = (bool) $flag;
        return $this;
    }

    /**
     * Is the element representing an array?
     *
     * @return bool
     */
    public function isArray() {
        return $this->_isArray;
    }

    /**
     * Set array to which element belongs
     *
     * @param  string $array
     * @return \ZendY\Report\Element
     */
    public function setBelongsTo($array) {
        $array = $this->filterName($array, true);
        if (!empty($array)) {
            $this->_belongsTo = $array;
        }

        return $this;
    }

    /**
     * Return array name to which element belongs
     *
     * @return string
     */
    public function getBelongsTo() {
        return $this->_belongsTo;
    }

    /**
     * Return element type
     *
     * @return string
     */
    public function getType() {
        if (null === $this->_type) {
            $this->_type = get_class($this);
        }

        return $this->_type;
    }

    /**
     * Set element attribute
     *
     * @param  string $name
     * @param  mixed $value
     * @return \ZendY\Report\Element
     * @throws Exception for invalid $name values
     */
    public function setAttrib($name, $value) {
        $name = (string) $name;
        if ('_' == $name[0]) {
            throw new Exception(sprintf('Invalid attribute "%s"; must not contain a leading underscore', $name));
        }

        if (null === $value) {
            unset($this->$name);
        } else {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * Set multiple attributes at once
     *
     * @param  array $attribs
     * @return \ZendY\Report\Element
     */
    public function setAttribs(array $attribs) {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }

    /**
     * Retrieve element attribute
     *
     * @param  string $name
     * @return string
     */
    public function getAttrib($name) {
        $name = (string) $name;
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * Return all attributes
     *
     * @return array
     */
    public function getAttribs() {
        $attribs = get_object_vars($this);
        unset($attribs['helper']);
        foreach ($attribs as $key => $value) {
            if ('_' == substr($key, 0, 1)) {
                unset($attribs[$key]);
            }
        }

        return $attribs;
    }

    /**
     * Overloading: retrieve object property
     *
     * Prevents access to properties beginning with '_'.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key) {
        if ('_' == $key[0]) {
            throw new Exception(sprintf('Cannot retrieve value for protected/private property "%s"', $key));
        }

        if (!isset($this->$key)) {
            return null;
        }

        return $this->$key;
    }

    /**
     * Overloading: set object property
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value) {
        $this->setAttrib($key, $value);
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
            $this->_isPartialRendering = true;
            $this->render();
            $this->_isPartialRendering = false;
            $decoratorName = substr($method, 6);
            if (false !== ($decorator = $this->getDecorator($decoratorName))) {
                $decorator->setElement($this);
                $seed = '';
                if (0 < count($args)) {
                    $seed = array_shift($args);
                }
                return $decorator->render($seed);
            }

            throw new Exception(sprintf('Decorator by name %s does not exist', $decoratorName));
        }

        throw new Exception(sprintf('Method %s does not exist', $method));
    }

    // Loaders

    /**
     * Set plugin loader to use for validator or filter chain
     *
     * @param  \Zend_Loader_PluginLoader_Interface $loader
     * @param  string $type 'decorator'
     * @return \ZendY\Report\Element
     * @throws Exception on invalid type
     */
    public function setPluginLoader(\Zend_Loader_PluginLoader_Interface $loader, $type) {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
                $this->_loaders[$type] = $loader;
                return $this;
            default:
                throw new Exception(sprintf('Invalid type "%s" provided to setPluginLoader()', $type));
        }
    }

    /**
     * Retrieve plugin loader for validator or filter chain
     *
     * Instantiates with default rules if none available for that type. Use
     * 'decorator', 'filter', or 'validate' for $type.
     *
     * @param  string $type
     * @return \Zend_Loader_PluginLoader
     * @throws \Zend_Loader_Exception on invalid type.
     */
    public function getPluginLoader($type) {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
                $prefixSegment = ucfirst(strtolower($type));
                $pathSegment = $prefixSegment;
                if (!isset($prefixSegment)) {
                    $prefixSegment = 'Report\Decorator';
                    $pathSegment = 'Report/Decorator';
                }
                if (!isset($this->_loaders[$type])) {
                    require_once 'Zend/Loader/PluginLoader.php';
                    $this->_loaders[$type] = new \Zend_Loader_PluginLoader(
                                    array('ZendY_' . $prefixSegment . '_' => 'ZendY/' . $pathSegment . '/')
                    );
                }
                return $this->_loaders[$type];
            default:
                require_once 'Zend/Loader/Exception.php';
                throw new \Zend_Loader_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Add prefix path for plugin loader
     *
     * If no $type specified, assumes it is a base path for both filters and
     * validators, and sets each according to the following rules:
     * - decorators: $prefix = $prefix . '_Decorator'
     *
     * Otherwise, the path prefix is set on the appropriate plugin loader.
     *
     * @param  string $prefix
     * @param  string $path
     * @param  string $type
     * @return \ZendY\Report\Element
     * @throws Exception for invalid type
     */
    public function addPrefixPath($prefix, $path, $type = null) {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
                $loader = $this->getPluginLoader($type);
                $loader->addPrefixPath($prefix, $path);
                return $this;
            case null:
                $nsSeparator = (false !== strpos($prefix, '\\')) ? '\\' : '_';
                $prefix = rtrim($prefix, $nsSeparator);
                $path = rtrim($path, DIRECTORY_SEPARATOR);
                foreach (array(self::DECORATOR) as $type) {
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
     * Add many prefix paths at once
     *
     * @param  array $spec
     * @return \ZendY\Report\Element
     */
    public function addPrefixPaths(array $spec) {
        if (isset($spec['prefix']) && isset($spec['path'])) {
            return $this->addPrefixPath($spec['prefix'], $spec['path']);
        }
        foreach ($spec as $type => $paths) {
            if (is_numeric($type) && is_array($paths)) {
                $type = null;
                if (isset($paths['prefix']) && isset($paths['path'])) {
                    if (isset($paths['type'])) {
                        $type = $paths['type'];
                    }
                    $this->addPrefixPath($paths['prefix'], $paths['path'], $type);
                }
            } elseif (!is_numeric($type)) {
                if (!isset($paths['prefix']) || !isset($paths['path'])) {
                    foreach ($paths as $prefix => $spec) {
                        if (is_array($spec)) {
                            foreach ($spec as $path) {
                                if (!is_string($path)) {
                                    continue;
                                }
                                $this->addPrefixPath($prefix, $path, $type);
                            }
                        } elseif (is_string($spec)) {
                            $this->addPrefixPath($prefix, $spec, $type);
                        }
                    }
                } else {
                    $this->addPrefixPath($paths['prefix'], $paths['path'], $type);
                }
            }
        }
        return $this;
    }

    // Rendering

    /**
     * Set view object
     *
     * @param  \Zend_View_Interface $view
     * @return \ZendY\Report\Element
     */
    public function setView(\Zend_View_Interface $view = null) {
        $this->_view = $view;
        return $this;
    }

    /**
     * Retrieve view object
     *
     * Retrieves from ViewRenderer if none previously set.
     *
     * @return null|\Zend_View_Interface
     */
    public function getView() {
        if (null === $this->_view) {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->setView($viewRenderer->view);
        }
        return $this->_view;
    }

    /**
     * Instantiate a decorator based on class name or class name fragment
     *
     * @param  string $name
     * @param  null|array $options
     * @return \ZendY\Report\Decorator\Interface
     */
    protected function _getDecorator($name, $options) {
        $class = $this->getPluginLoader(self::DECORATOR)->load($name);
        if (null === $options) {
            $decorator = new $class;
        } else {
            $decorator = new $class($options);
        }

        return $decorator;
    }

    /**
     * Add a decorator for rendering the element
     *
     * @param  string|\ZendY\Report\Decorator\Interface $decorator
     * @param  array|\Zend_Config $options Options with which to initialize decorator
     * @return \ZendY\Report\Element
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
            throw new Exception('Invalid decorator provided to addDecorator; must be string or ZendY\Report\Decorator\Interface');
        }

        $this->_decorators[$name] = $decorator;

        return $this;
    }

    /**
     * Add many decorators at once
     *
     * @param  array $decorators
     * @return \ZendY\Report\Element
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
     * Overwrite all decorators
     *
     * @param  array $decorators
     * @return \ZendY\Report\Element
     */
    public function setDecorators(array $decorators) {
        $this->clearDecorators();
        return $this->addDecorators($decorators);
    }

    /**
     * Retrieve a registered decorator
     *
     * @param  string $name
     * @return false|\ZendY\Report\Decorator\Abstract
     */
    public function getDecorator($name) {
        if (!isset($this->_decorators[$name])) {
            $len = strlen($name);
            foreach ($this->_decorators as $localName => $decorator) {
                if ($len > strlen($localName)) {
                    continue;
                }

                if (0 === substr_compare($localName, $name, -$len, $len, true)) {
                    if (is_array($decorator)) {
                        return $this->_loadDecorator($decorator, $localName);
                    }
                    return $decorator;
                }
            }
            return false;
        }

        if (is_array($this->_decorators[$name])) {
            return $this->_loadDecorator($this->_decorators[$name], $name);
        }

        return $this->_decorators[$name];
    }

    /**
     * Retrieve all decorators
     *
     * @return array
     */
    public function getDecorators() {
        foreach ($this->_decorators as $key => $value) {
            if (is_array($value)) {
                $this->_loadDecorator($value, $key);
            }
        }
        return $this->_decorators;
    }

    /**
     * Remove a single decorator
     *
     * @param  string $name
     * @return \ZendY\Report\Element
     */
    public function removeDecorator($name) {
        if (isset($this->_decorators[$name])) {
            unset($this->_decorators[$name]);
        } else {
            $len = strlen($name);
            foreach (array_keys($this->_decorators) as $decorator) {
                if ($len > strlen($decorator)) {
                    continue;
                }
                if (0 === substr_compare($decorator, $name, -$len, $len, true)) {
                    unset($this->_decorators[$decorator]);
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Clear all decorators
     *
     * @return \ZendY\Report\Element
     */
    public function clearDecorators() {
        $this->_decorators = array();
        return $this;
    }

    /**
     * Render report element
     *
     * @param  \Zend_View_Interface $view
     * @return string
     */
    public function render(\Zend_View_Interface $view = null) {
        if ($this->_isPartialRendering) {
            return '';
        }

        if (null !== $view) {
            $this->setView($view);
        }

        $content = '';
        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
        return $content;
    }

    /**
     * String representation of report element
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
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
    }

    /**
     * Lazy-load a decorator
     *
     * @param  array $decorator Decorator type and options
     * @param  mixed $name Decorator name or alias
     * @return \ZendY\Report\Decorator\Interface
     */
    protected function _loadDecorator(array $decorator, $name) {
        $sameName = false;
        if ($name == $decorator['decorator']) {
            $sameName = true;
        }

        $instance = $this->_getDecorator($decorator['decorator'], $decorator['options']);
        if ($sameName) {
            $newName = get_class($instance);
            $decoratorNames = array_keys($this->_decorators);
            $order = array_flip($decoratorNames);
            $order[$newName] = $order[$name];
            $decoratorsExchange = array();
            unset($order[$name]);
            asort($order);
            foreach ($order as $key => $index) {
                if ($key == $newName) {
                    $decoratorsExchange[$key] = $instance;
                    continue;
                }
                $decoratorsExchange[$key] = $this->_decorators[$key];
            }
            $this->_decorators = $decoratorsExchange;
        } else {
            $this->_decorators[$name] = $instance;
        }

        return $instance;
    }

}
