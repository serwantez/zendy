<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Code\Generator\Php\Zend;

use Zend2\Code\Generator;

/**
 * Generator kontrolerów i akcji
 *
 * @author Piotr Zając
 */
class Controller extends Generator\FileGenerator {
    /**
     * Parametry
     */

    const PARAM_ACTION = 'Action';
    const PARAM_CONTROLLER = 'Controller';

    /**
     * Ścieżka dostępu dla kontrolerów
     * 
     * @var string 
     */
    private $_controllersPath = '../application/controllers/';

    /**
     * Ścieżka dostępu dla widoków
     * 
     * @var string 
     */
    private $_viewsPath = '../application/views/scripts/';

    /**
     * Ścieżka dostępu dla klas formularzy
     * 
     * @var string 
     */
    private $_formsPath = '../application/forms/';

    /**
     * Nazwa kontrolera
     * 
     * @var string
     */
    protected $_name;

    /**
     * Twórca plików
     * 
     * @var string
     */
    protected $_author = '';

    /**
     * Konstruktor
     * 
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'Index', $options = array()) {
        parent::__construct($options);
        $this->_setName($name);

        $dir = $this->_viewsPath . $this->getName();
        //utworzenie katalogu dla widoków akcji
        if (!is_dir($dir)) {
            try {
                mkdir($dir);
            } catch (Exception $exc) {
                throw $exc;
            }
        }
        if (file_exists($this->getFilename())) {
            $file = Generator\FileGenerator::fromReflectedFileName(
                            '../application/controllers/' . $this->getFullName() . '.php', true
            );
            $this->setClasses($file->getClasses());
            $this->setNamespace($file->getNamespace());
            $this->setUses($file->getUses());
        } else {
            $bootstrap = \Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $options = $bootstrap->getOptions();
            if (array_key_exists('author', $options)) {
                $this->_author = $options['author'];
            } else {
                $this->_author = '';
            }
            $docblock = new Generator\DocBlockGenerator(
                            $this->getFullName(),
                            'Kontroler ' . $this->getName(),
                            array(
                                array(
                                    'name' => 'author',
                                    'description' => $this->_author,
                                ),
                            )
            );
            $class = new Generator\ClassGenerator();
            $class->setDocblock($docblock);
            $class->setName($this->getFullName());
            $class->setExtendedClass('Zend_Controller_Action');
            $this->setClass($class);
        }
    }

    /**
     * Ustawia nazwę kontrolera
     * 
     * @param string $name
     * @return \ZendY\Code\Generator\Php\Zend\Controller
     */
    protected function _setName($name) {
        $this->_name = lcfirst($name);
        $this->setFilename($this->_controllersPath . $this->getFullName() . '.php');
        return $this;
    }

    /**
     * Zwraca krótką nazwę kontrolera
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Zwraca długą nazwę kontrolera
     * 
     * @return string
     */
    public function getFullName() {
        return ucfirst($this->getName()) . self::PARAM_CONTROLLER;
    }

    /**
     * Tworzy plik widoku akcji
     * 
     * @param string $actionName
     * @param string $body
     * @return \ZendY\Code\Generator\Php\Zend\Controller
     */
    protected function _createView($actionName, $body = null) {
        $fileName = $this->_viewsPath . $this->getName() . '/' . $actionName . '.phtml';
        if (!file_exists($fileName)) {
            $cg = new Generator\FileGenerator();
            $cg->setFilename($fileName);
            if (isset($body))
                $cg->setBody($body);
            $cg->write();
        }
        return $this;
    }

    /**
     * Tworzy plik klasy formularza
     * 
     * @param string $formName
     * @param string $body
     * @return \ZendY\Code\Generator\Php\Zend\Controller
     */
    protected function _createForm($formName, $body = null) {
        $formName = ucfirst($formName);
        $fileName = $this->_formsPath . $formName . '.php';
        if (!file_exists($fileName)) {
            $cg = new Generator\FileGenerator();
            $cg->setFilename($fileName);
            $cg->setNamespace('Application\Form');
            $cg->setUses(array(
                array('ZendY\Css'),
                array('ZendY\Db\DataSource'),
                array('ZendY\Db\Form')
            ));
            //kod poza klasą
            if (isset($body))
                $cg->setBody($body);
            //stworzenie klasy formularza
            $formClass = new Generator\ClassGenerator();
            $docblock = new Generator\DocBlockGenerator(
                            $formName,
                            'Formularz ' . $formName,
                            array(
                                array(
                                    'name' => 'author',
                                    'description' => $this->_author,
                                ),
                            )
            );
            $formClass->setName($formName)
                    ->setExtendedClass('Form')
                    ->setDocblock($docblock);
            //metoda inicjalizująca
            $init = new Generator\MethodGenerator();
            $init->setName('init');
            $formClass->addMethodFromGenerator($init);
            $cg->setClass($formClass);
            $cg->write();
        }
        return $this;
    }

    /**
     * Tworzy akcję
     * 
     * @param string $name
     * @param array $parameters
     * @param bool $view
     * @param string $actionBody
     * @param DocBlockGenerator|string $docblock
     * @param string $viewBody
     * @return \Zend2\Code\Generator\MethodGenerator
     */
    protected function _createAction($name, $parameters = array(), $view = false, $actionBody = null, $docblock = null, $viewBody = null) {
        $method = new Generator\MethodGenerator();
        foreach ($parameters as $key => $param) {
            $parameters[$key] = new Generator\ParameterGenerator($param);
        }
        $method->setName($name . self::PARAM_ACTION)
                ->setParameters($parameters);
        if (isset($actionBody)) {
            $method->setBody($actionBody);
            if (isset($viewBody)) {
                $this->setUse(sprintf('Application\Form\%s', ucfirst($name)));
                $this->_createForm($name);
            }
        }
        if (isset($docblock)) {
            $method->setDocblock($docblock);
        }
        if ($view) {
            $this->_createView($name, $viewBody);
        }
        return $method;
    }

    /**
     * Dodaje metodę do klasy konstruktora
     * 
     * @param string $name
     * @param array $parameters
     * @param string $body
     * @param DocBlockGenerator|string $docblock
     * @return \ZendY\Code\Generator\Php\Zend\Controller
     */
    public function addMethod($name, $parameters = array(), $body = null, $docblock = null) {
        $class = $this->getClass($this->getFullName());
        if ($class->hasMethod($name) == false) {
            $method = new Generator\MethodGenerator();
            foreach ($parameters as $key => $param) {
                $parameters[$key] = new Generator\ParameterGenerator($param);
            }
            $method->setName($name)
                    ->setParameters($parameters);
            if (isset($body))
                $method->setBody($body);
            if (isset($docblock))
                $method->setDocblock($docblock);

            $class->addMethodFromGenerator($method);
        }
        return $this;
    }

    /**
     * Dodaje akcję
     * 
     * @param string $name
     * @param array $parameters
     * @param bool $view
     * @param string $actionBody
     * @param DocBlockGenerator|string $docblock
     * @param string $viewBody
     * @return \ZendY\Code\Generator\Php\Zend\Controller
     */
    public function addAction($name, $parameters = array(), $view = false, $actionBody = null, $docblock = null, $viewBody = null) {
        $name = lcfirst($name);
        $class = $this->getClass($this->getFullName());
        if ($class->hasMethod($name . self::PARAM_ACTION) == false) {
            $class->addMethodFromGenerator(
                    $this->_createAction($name, $parameters, $view, $actionBody, $docblock, $viewBody)
            );
        }
        return $this;
    }

    /**
     * Zapisuje plik kontrolera na dysku
     * 
     * @return \ZendY\Code\Generator\Php\Zend\Controller
     */
    public function write() {
        $class = $this->getClass($this->getFullName());
        if ($class->isSourceDirty())
            parent::write();
        return $this;
    }

}