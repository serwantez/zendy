<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

use ZendY\Object;
use ZendY\Exception;

/**
 * Klasa bazowa dla wszystkich komponentów. 
 * Komponentami mogą być zarówno obiekty widoczne jak i niewidoczne. 
 *
 * @author Piotr Zając
 */
abstract class Component extends Object {
    /**
     * Stałe
     */

    const DECORATOR = 'DECORATOR';

    /**
     * Tablica dekoratorów
     * 
     * @var array 
     */
    protected $_decorators = array();

    /**
     * Should we disable loading the default decorators?
     * 
     * @var bool
     */
    protected $_disableLoadDefaultDecorators = false;

    /**
     * Plugin loaders for filter and validator chains
     * 
     * @var array
     */
    protected $_loaders = array();

    /**
     * Adapter tłumaczeń
     * 
     * @var \Zend_Translate
     */
    protected $_translator;

    /**
     * Domyślny adapter tłumaczeń (globalny)
     * 
     * @var \Zend_Translate
     */
    protected static $_translatorDefault;

    /**
     * Czy tłumaczenie ma być wyłączone?
     * 
     * @var bool
     */
    protected $_translatorDisabled = false;

    /**
     * Widok obiektu
     * 
     * @var \Zend_View_Interface 
     */
    protected $_view;

    /**
     * Constructor
     *
     * @param  string|null $id
     * @param  array|Zend_Config|null $options
     * @return void
     */
    public function __construct($id = null, $options = null) {
        parent::__construct($id);

        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof \Zend_Config) {
            $this->setConfig($options);
        }

        /**
         * Extensions
         */
        $this->init();

        /**
         * Register decorators by default
         */
        $this->loadDefaultDecorators();
    }

    /**
     * Inicjalizuje obiekt, funkcja używana przez klasy potomne
     *
     * @return void
     */
    public function init() {
        
    }

    /**
     * Ustawia atrybut o podanej nazwie
     * 
     * @param string $name
     * @param type $value
     * @return \ZendY\Component
     * @throws Exception
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
     * Ustawia wiele atrybutów na raz
     *
     * @param  array $attribs
     * @return \ZendY\Component
     */
    public function setAttribs(array $attribs) {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }
        return $this;
    }

    /**
     * Zwraca atrybut o podanej nazwie
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
     * Zwraca wszystkie atrybuty
     *
     * @return array
     */
    public function getAttribs() {
        $attribs = get_object_vars($this);
        foreach ($attribs as $key => $value) {
            if ('_' == substr($key, 0, 1)) {
                unset($attribs[$key]);
            }
        }
        return $attribs;
    }

    /**
     * Odczytuje atrybut
     * 
     * @param  string $key
     * @return mixed
     */
    public function __get($key) {
        //Prevents access to properties beginning with '_'.
        if ('_' == $key[0]) {
            throw new Exception(sprintf('Cannot retrieve value for protected/private property "%s"', $key));
        }

        if (!isset($this->$key)) {
            return null;
        }
        return $this->$key;
    }

    /**
     * Ustawia atrybut
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value) {
        $this->setAttrib($key, $value);
    }

    /**
     * Add prefix path for plugin loader
     *
     * @param  string $prefix
     * @param  string $path
     * @param  string $type
     * @return \ZendY\Component
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
     * @return \ZendY\Component
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

    /**
     * Set object state from options array
     *
     * @param  array $options
     * @return \ZendY\Component
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
     * @return \ZendY\Component
     */
    public function setConfig(\Zend_Config $config) {
        return $this->setOptions($config->toArray());
    }

    /**
     * Ustawia widok obiektu
     *
     * @param  \Zend_View_Interface $view
     * @return \ZendY\Component
     */
    public function setView(\Zend_View_Interface $view = null) {
        if (null !== $view) {
            if (false === $view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) {
                $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
            }
        }
        $this->_view = $view;
        return $this;
    }

    /**
     * Zwraca widok obiektu
     *
     * @return \Zend_View_Interface
     */
    public function getView() {
        //Retrieves from ViewRenderer if none previously set.
        if (null === $this->_view) {
            $viewRenderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->setView($viewRenderer->view);
        }
        return $this->_view;
    }

    /**
     * Set translator object for localization
     *
     * @param  \Zend_Translate|null $translator
     * @return \ZendY\Component
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
     * Set global default translator object
     *
     * @param  \Zend_Translate|\Zend_Translate_Adapter|null $translator
     * @return void
     */
    public static function setDefaultTranslator($translator = null) {
        if (null === $translator) {
            self::$_translatorDefault = null;
        } elseif ($translator instanceof \Zend_Translate_Adapter) {
            self::$_translatorDefault = $translator;
        } elseif ($translator instanceof \Zend_Translate) {
            self::$_translatorDefault = $translator->getAdapter();
        } else {
            throw new Exception('Invalid translator specified');
        }
    }

    /**
     * Is there a default translation object set?
     *
     * @return bool
     */
    public static function hasDefaultTranslator() {
        return (bool) self::$_translatorDefault;
    }

    /**
     * Get global default translator object
     *
     * @return null|\Zend_Translate
     */
    public static function getDefaultTranslator() {
        if (null === self::$_translatorDefault) {
            require_once 'Zend/Registry.php';
            if (\Zend_Registry::isRegistered('Zend_Translate')) {
                $translator = \Zend_Registry::get('Zend_Translate');
                if ($translator instanceof \Zend_Translate_Adapter) {
                    return $translator;
                } elseif ($translator instanceof \Zend_Translate) {
                    return $translator->getAdapter();
                }
            }
        }
        return self::$_translatorDefault;
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
            return \Zend_Form::getDefaultTranslator();
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
     * @return \ZendY\Component
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

    /**
     * Instantiate a decorator based on class name or class name fragment
     *
     * @param  string $name
     * @param  null|array $options
     * @return \Zend_Form_Decorator_Interface
     */
    protected function _getDecorator($name, $options) {
        $class = $this->getPluginLoader(self::DECORATOR)->load($name);
        if (null === $options) {
            $decorator = new $class($this);
        } else {
            $decorator = new $class($this, $options);
        }

        return $decorator;
    }

    /**
     * Lazy-load a decorator
     *
     * @param  array $decorator Decorator type and options
     * @param  mixed $name Decorator name or alias
     * @return \ZendY\Component
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

    /**
     * Set plugin loader to use for validator or filter chain
     *
     * @param  \Zend_Loader_PluginLoader_Interface $loader
     * @param  string $type
     * @return \ZendY\Component
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
     * Instantiates with default rules if none available for that type.
     *
     * @param string $type
     * @return \Zend_Loader_PluginLoader
     */
    public function getPluginLoader($type) {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
                if (!isset($prefixSegment)) {
                    $prefixSegment = 'Form_Decorator';
                    $pathSegment = 'Form/Decorator';
                }
                if (!isset($this->_loaders[$type])) {
                    $this->_loaders[$type] = new \Zend_Loader_PluginLoader(
                                    array('ZendY_' . $prefixSegment . '_' => 'ZendY/' . $pathSegment . '/')
                    );
                }
                return $this->_loaders[$type];
            default:
                throw new Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Dodaje dekorator
     *
     * @param  string|\Zend_Form_Decorator_Interface $decorator
     * @param  array|\Zend_Config $options Options with which to initialize decorator
     * @return \ZendY\Component
     */
    public function addDecorator($decorator, $options = null) {
        if ($decorator instanceof \Zend_Form_Decorator_Interface) {
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
            } elseif ($spec instanceof \Zend_Form_Decorator_Interface) {
                $decorator = $spec;
            }
        } else {
            throw new Exception('Invalid decorator provided to addDecorator; must be string or Zend_Form_Decorator_Interface');
        }

        $this->_decorators[$name] = $decorator;

        return $this;
    }

    /**
     * Dodaje na raz wiele dekoratorów
     *
     * @param  array $decorators
     * @return \ZendY\Component
     */
    public function addDecorators(array $decorators) {
        foreach ($decorators as $decoratorName => $decoratorInfo) {
            if (is_string($decoratorInfo) ||
                    $decoratorInfo instanceof \Zend_Form_Decorator_Interface) {
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
     * Ustawia dekoratory (zastępuje istniejące)
     *
     * @param  array $decorators
     * @return \ZendY\Component
     */
    public function setDecorators(array $decorators) {
        $this->clearDecorators();
        return $this->addDecorators($decorators);
    }

    /**
     * Zwraca dekorator o podanej nazwie
     *
     * @param  string $name
     * @return false|\Zend_Form_Decorator_Abstract
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
     * Zwraca wszystkie dekoratory
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
     * Usuwa dekorator o podanej nazwie
     *
     * @param  string $name
     * @return \ZendY\Component
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
     * Usuwa wszystkie dekoratory
     *
     * @return \ZendY\Component
     */
    public function clearDecorators() {
        $this->_decorators = array();
        return $this;
    }

    /**
     * Set flag to disable loading default decorators
     *
     * @param  bool $flag
     * @return \ZendY\Component
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
     * @return \ZendY\Component
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }
        return $this;
    }

}