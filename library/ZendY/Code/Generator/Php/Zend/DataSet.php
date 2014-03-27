<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Code\Generator\Php\Zend;

use Zend2\Code\Generator;

/**
 * Generator klas zbiorów danych
 *
 * @author Piotr Zając
 */
class DataSet extends Generator\FileGenerator {

    /**
     * Ścieżka dostępu dla modeli
     * 
     * @var string 
     */
    private $_modelsPath = '../application/models/';

    /**
     * Nazwa modelu
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
     * Kolumny tabeli
     * 
     * @var array
     */
    protected $_cols = array();

    /**
     * Kolumny klucza głównego
     * 
     * @var array
     */
    protected $_primary = array();

    /**
     * Konstruktor
     * 
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options = array()) {
        parent::__construct($options);
        $this->_setName($name);

        //autor pliku
        $bootstrap = \Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();
        if (array_key_exists('author', $options)) {
            $this->_author = $options['author'];
        } else {
            $this->_author = '';
        }
        $this->_getTableInfo($this->getName());
        $this->_createDataSet();
    }

    /**
     * Ustawia nazwę zbioru danych
     * 
     * @param string $name
     * @return \ZendY\Code\Generator\Php\Zend\DataSet
     */
    protected function _setName($name) {
        $this->_name = lcfirst($name);
        $this->setFilename($this->_modelsPath . ucfirst($name) . '.php');
        return $this;
    }

    /**
     * Zwraca nazwę zbioru danych
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Tworzy plik klasy zbioru danych
     * 
     * @param string $formName
     * @param string $body
     * @return \ZendY\Code\Generator\Php\Zend\DataSet
     */
    protected function _createDataSet() {
        $className = ucfirst($this->getName());

        $docblock = new Generator\DocBlockGenerator(
                        'Zbiór danych ' . $className,
                        null,
                        array(
                            array(
                                'name' => 'author',
                                'description' => $this->_author,
                            ),
                        )
        );

        //główna klasa zbioru
        $class = new Generator\ClassGenerator();
        $class->setDocblock($docblock);
        $class->setName($className);
        $class->setExtendedClass('DataSet\Table');

        foreach ($this->_cols as $key => $col) {
            $name = 'COL_' . strtoupper($col);
            $val = $col;
            $const = new Generator\PropertyGenerator($name, $val, Generator\PropertyGenerator::FLAG_CONSTANT);
            if ($key == 0) {
                $const->setDocBlock('Kolumny zbioru');
            }
            $class->addPropertyFromGenerator($const);
        }

        $const = new Generator\PropertyGenerator('TABLE_NAME', $this->getName(), Generator\PropertyGenerator::FLAG_CONSTANT);
        $const->setDocBlock('Nazwa tabeli');
        $class->addPropertyFromGenerator($const);

        //metoda inicjalizująca
        $init = new Generator\MethodGenerator();
        $init->setName('init');
        $body[] = 'parent::init();';
        $body[] = '$this->setTableName(self::TABLE_NAME);';
        foreach ($this->_primary as $col) {
            $primaryCols[] = 'self::COL_' . strtoupper($col);
        }
        $body[] = sprintf('$this->setPrimary(array(%s));', implode(',', $primaryCols));
        $init->setBody(implode("\n", $body));
        $class->addMethodFromGenerator($init);

        //poza klasą
        $docblock = new Generator\DocBlockGenerator(
                        'ZendY',
                        null,
                        array(
                            array(
                                'name' => 'copyright',
                                'description' => 'E-FISH sp. z o.o. (http://www.efish.pl/)',
                            ),
                        )
        );
        $this->setDocBlock($docblock);
        $this->setNamespace('Application\Model');
        $this->setUses(array(
            array('ZendY\Msg'),
            array('ZendY\Db'),
            array('ZendY\Db\DataSet')
        ));
        $this->setClass($class);

        return $this;
    }

    protected function _getTableInfo($name) {
        $db = \Zend_Registry::get('db');
        $table = new \Zend_Db_Table(array(
                    \Zend_Db_Table_Abstract::ADAPTER => $db,
                    \Zend_Db_Table_Abstract::NAME => $name
                ));
        $this->_cols = $table->info(\Zend_Db_Table_Abstract::COLS);
        $this->_primary = $table->info(\Zend_Db_Table_Abstract::PRIMARY);
        return $this;
    }

    public function write() {
        if (!file_exists($this->getFilename())) {
            parent::write();
        }
    }

}