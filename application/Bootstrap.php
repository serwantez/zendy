<?php

use ZendY\Acl;
use ZendY\Db\DataSet\App\Page;

/**
 * @author Piotr Zając
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    private $view;
    private $acl;

    protected function _initAutoloader() {
        $applicationAutoloader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => 'Application_',
                    'basePath' => dirname(__FILE__),
                ));

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(array('Bootstrap', 'autoload'), 'Application\\Form\\');
        $autoloader->pushAutoloader(array('Bootstrap', 'autoload'), 'Application\\Model\\');
    }

    static public function autoload($className) {
        Zend_Loader_Autoloader::autoload(str_replace('\\', '_', $className));
    }

    protected function _initBaseUrl() {
        $this->bootstrap("frontController");
        $front = $this->getResource("frontController");
        $request = new Zend_Controller_Request_Http();
        $front->setRequest($request);
    }

    protected function _initDefaults() {
        //nazwa kontrolera bazodanowego
        ZendY\Db\DataSource::$controller = '/data/';
        //folder pobieranych plików
        Blueimp\Upload\Handler::$uploadDir = 'application/images/uploaded/';
        //folder przechowywania miniatur
        Blueimp\Upload\Handler::$thumbnailDir = 'library/components/fileupload/server/php/thumbnails/';
    }

    /**
     * inicjalizacja dostępu do bazy danych
     */
    protected function _initMyDb() {
        //pobranie adaptera bazy danych
        $this->bootstrap('db');
        $resource = $this->getPluginResource('db');
        $db = $resource->getDbAdapter();

        //zapisanie adaptera bazy w rejestrze
        Zend_Registry::set('db', $db);

        //sprawdzenie struktury bazy danych
        //ZendY\Db\Mysql::verify($db);
    }

    protected function _initView() {
        $this->view = new Zend_View();
        $this->view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
        $this->view->addHelperPath('ZendY/View/Helper/', 'ZendY\View\Helper');

        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($this->view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        $this->view->host = '';
    }

    protected function _initLanguage() {
        $namespace = new Zend_Session_Namespace();
        //ustawienia terytorialne
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $locale = null;
        if ($request->getParam('localeList')) {
            $locale = $request->getParam('localeList');
        } elseif (isset($namespace->locale))
            $locale = $namespace->locale;
        else
        //domyślnie ustawienia dla Polski
            $locale = 'pl_PL';
        $zl = new Zend_Locale($locale);
        //zapisuje ustawienia terytorialne w rejestrze
        Zend_Registry::set('Zend_Locale', $zl);
        //zapisuje ustawienia terytorialne w sesji
        $namespace->locale = $zl;

        //ustawienia językowe
        $this->view->language = $zl->getLanguage();
        $translate = new Zend_Translate(
                        array(
                            'adapter' => 'array',
                            'content' => realpath(APPLICATION_PATH . '/../resources/languages/' . $this->view->language),
                            'locale' => $zl,
                            'scan' => Zend_Translate::LOCALE_DIRECTORY
                        )
        );

        Zend_Registry::set('Zend_Translate', $translate);

        $f = new Application\Form\Locale();
        $this->view->localeForm = $f->render();
    }

    protected function _initACL() {
        $this->acl = new Acl();
        $this->acl->addRoles();
        $this->acl->addResources();
        $this->acl->addRules();

        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new ZendY\Controller\Plugin\Acl($this->acl));
    }

    /**
     * inicjalizacja nawigacji
     */
    protected function _initNavigation() {
        //pobranie zasobu szablonu strony
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        //pobranie danych do nawigacji
        $mdl = new Page();
        $pages = $mdl->toMultiDimensionalArray();
        $nav = new Zend_Navigation($pages);
        $view->navigation($nav)->setAcl($this->acl)->setRole(Zend_Registry::get('role'));
    }

}
