<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Code\Generator\Php\Zend;

use Zend2\Code\Generator;
use ZendY\Db\Mysql;

/**
 * Generator formularza bazodanowego
 *
 * @author Piotr Zając
 */
class DataForm extends Generator\FileGenerator {

    /**
     * Ścieżka dostępu dla klas formularzy
     * 
     * @var string 
     */
    private $_formsPath = '../application/forms/';

    /**
     * Nazwa formularza
     * 
     * @var string
     */
    protected $_name;

    /**
     * Ścieżka do pliku z szablonem
     * 
     * @var string
     */
    protected $_patternFile;

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
    public function __construct($name, $options = array()) {
        parent::__construct($options);
        $this->_setName($name);

        $bootstrap = \Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();
        if (array_key_exists('author', $options)) {
            $this->_author = $options['author'];
        } else {
            $this->_author = '';
        }
        $this->_createForm();
    }

    /**
     * Ustawia ścieżkę do pliku z szablonem
     * 
     * @param string $file
     * @return \ZendY\Code\Generator\Php\Zend\DataForm
     */
    public function setPatternFile($file) {
        $this->_patternFile = $file;
        return $this;
    }

    /**
     * Zwraca ścieżkę do pliku z szablonem

     * @return string
     */
    public function getPatternFile() {
        return $this->_patternFile;
    }

    /**
     * Ustawia nazwę kontrolera
     * 
     * @param string $name
     * @return \ZendY\Code\Generator\Php\Zend\DataForm
     */
    protected function _setName($name) {
        $this->_name = lcfirst($name);
        $this->setFilename($this->_formsPath . ucfirst($this->getName()) . '.php');
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

    private function _getData($dataSet, $modelName) {
        $data = array(
            'columns' => '',
            'edit' => array(
                'def' => '',
                'var' => ''
            ),
            'filter' => array(
                'def' => '',
                'var' => ''
            )
        );
        $columns = $dataSet->describe();
        $code = array();
        foreach ($columns as $key => $column) {
            if (Mysql::isNumeric($column['DATA_TYPE'])) {
                $align = 'Css::TEXT_ALIGN_HORIZONTAL_RIGHT';
                $width = 90;
            } else {
                $align = 'Css::TEXT_ALIGN_HORIZONTAL_LEFT';
                $width = 200;
            }
            $code[] = "new Element\Grid\Column(";
            $code[] = "array(";
            $code[] = sprintf("'name' => '%s',", $column['COLUMN_NAME']);
            $code[] = sprintf("'label' => '%s',", ucfirst($column['COLUMN_NAME']));
            $code[] = sprintf("'width' => %s,", $width);
            $code[] = sprintf("'align' => %s", $align);
            $code[] = ")),";

            $edit['var'][] = '$' . $column['COLUMN_NAME'];
            $edit['def'][] = sprintf("$%s = new DbElement\Edit(array(", $column['COLUMN_NAME']);
            $edit['def'][] = sprintf("'name' => 'edit%s',", ucfirst($column['COLUMN_NAME']));
            $edit['def'][] = sprintf('\'dataSource\' => $dataSource%s,', $modelName);
            $edit['def'][] = sprintf("'dataField' => '%s',", $column['COLUMN_NAME']);
            $edit['def'][] = "'width' => 250,";
            $edit['def'][] = sprintf("'label' => '%s',", ucfirst($column['COLUMN_NAME']));
            $edit['def'][] = "));";
            $edit['def'][] = "";

            $filter['var'][] = '$filter' . ucfirst($column['COLUMN_NAME']);
            $filter['def'][] = sprintf('$filter%s = new DbElement\Filter\IconEdit(array(', ucfirst($column['COLUMN_NAME']));
            $filter['def'][] = sprintf("'name' => 'filter%s',", ucfirst($column['COLUMN_NAME']));
            $filter['def'][] = sprintf('\'dataSource\' => $dataSource%s,', $modelName);
            $filter['def'][] = sprintf("'dataField' => '%s',", $column['COLUMN_NAME']);
            $filter['def'][] = "'width' => 250,";
            $filter['def'][] = sprintf("'label' => '%s',", ucfirst($column['COLUMN_NAME']));
            $filter['def'][] = "));";
            $filter['def'][] = "";
        }
        $data['columns'] = implode("\n", $code);
        $data['edit']['def'] = implode("\n", $edit['def']);
        $data['edit']['var'] = implode(", ", $edit['var']);
        $data['filter']['def'] = implode("\n", $filter['def']);
        $data['filter']['var'] = implode(", ", $filter['var']);
        return $data;
    }

    /**
     * Tworzy plik klasy formularza
     * 
     * @param string $body
     * @return \ZendY\Code\Generator\Php\Zend\DataForm
     */
    protected function _createForm($body = null) {
        $formName = ucfirst($this->getName());
        $this->setNamespace('Application\Form');
        $this->setUses(array(
            array('ZendY\Css'),
            array('ZendY\Db\DataSource'),
            array('ZendY\Db\Form'),
            array('ZendY\Db\DataSet'),
            array('ZendY\Form\Element'),
            array('use' => 'ZendY\Db\Form\Element', 'as' => 'DbElement'),
            array('ZendY\Form\Container'),
            array('use' => 'ZendY\Db\Form\Container', 'as' => 'DbContainer'),
            array('Application\Model'),
        ));
        //kod poza klasą
        if (isset($body))
            $this->setBody($body);
        //stworzenie klasy formularza
        $formClass = new Generator\ClassGenerator();
        $docblock = new Generator\DocBlockGenerator(
                        $formName,
                        'Formularz ' . $this->getName(),
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
        $model = $this->getName();
        $body = file_get_contents($this->_patternFile);
        $className = "\Application\Model\\" . ucfirst($model);
        $dataSet = new $className(array('name' => $model));
        $data = $this->_getData($dataSet, ucfirst($model));
        $body = str_replace(array(
            '<?php',
            '_model_',
            '_umodel_',
            '_columns_',
            '_editControlsDef_;',
            '_editControls_',
            '_filterControlsDef_;',
            '_filterControls_'
                ), array(
            '',
            $model,
            ucfirst($model),
            $data['columns'],
            $data['edit']['def'],
            $data['edit']['var'],
            $data['filter']['def'],
            $data['filter']['var']
                ), $body);
        $init->setBody($body);
        $formClass->addMethodFromGenerator($init);
        $this->setClass($formClass);
        return $this;
    }

    /**
     * Zapisuje plik formularza na dysku
     * 
     * @return \ZendY\Code\Generator\Php\Zend\DataForm
     */
    public function write() {
        if (!file_exists($this->getFilename())) {
            parent::write();
        }
        return $this;
    }

}