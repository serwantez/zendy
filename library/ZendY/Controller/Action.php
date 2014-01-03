<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Controller;

require_once 'Zend/Controller/Action.php';

use ZendY\Msg;
use ZendY\Db\ActionManager;

/**
 * Kontroler zawierające akcje pobierania danych za pomocą zapytań ajaxowych
 *
 * @author Piotr Zając
 */
abstract class Action extends \Zend_Controller_Action {

    /**
     * Akcja ajaxowej walidacji formularza
     * 
     * @return void
     */
    public function validateformAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $param = $this->_getAllParams();
        $f = new $param['form'];
        unset($param['form']);
        $f->isValid($param);
        $messages = $f->getMessages();
        $data = \ZendY\Form::prepareFormMessages($f, $messages);
        header('Content-type: application/json');
        echo \Zend_Json::encode($data);
    }

    /**
     * Akcja wyświetlania podpowiedzi dla kontrolki AutoComplete
     * 
     * @return void
     */
    public function autocompleteAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $param = $this->_getAllParams();

        $results = array();

        if (array_key_exists('id', $param) && array_key_exists('field', $param)) {
            $dbs = new \Zend_Session_Namespace('db');
            if (isset($dbs->dataset) && array_key_exists($param['id'], $dbs->dataset)) {
                $dataSet = unserialize($dbs->dataset[$param['id']]);
                $dataSet->setFilter('autocomplete', array(
                    $param['field'] => array(
                        'value' => $this->_getParam('term'),
                        'operator' => \ZendY\Db\DataSet\Base::OPERATOR_BEGIN
                    )
                ));
                $results = $dataSet->fetchCol($param['field']);
            }
        }
        $this->_helper->json(array_values($results));
    }

    /**
     * Akcja wyświetlania grafiki bazodanowej
     * 
     * @return void
     */
    public function imageAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $param = $this->_getAllParams();
        if (array_key_exists('id', $param) && array_key_exists('field', $param)) {
            $dbs = new \Zend_Session_Namespace('db');
            if (isset($dbs->datasource) && array_key_exists($param['id'], $dbs->datasource)) {
                $dataSource = new \ZendY\Db\DataSource();
                $dataSource->cloneThis(unserialize($dbs->datasource[$param['id']]));
                $dataSet = $dataSource->getDataSet();
                if ($dataSet->getState()
                        && $dataSet->getRecordCount()
                        && ($blob = $dataSet->fetchOne($param['field']))
                        && !(array_key_exists('value', $param) && $param['value'] == 'empty')
                ) {
                    header("Content-type: image/png");
                    echo $blob;
                } else {
                    header("Content-type: image/png");
                    $image = imagecreatefrompng('application/images/uploaded/noimage.png');
                    imagepng($image);
                    imagedestroy($image);
                }
            }
        }
    }

    /**
     * Akcja wykonująca zdarzenie na zbiorze danych i zwracająca dane do kontrolek bazodanowych
     * 
     * @return void
     */
    public function dataAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        Msg::add(__METHOD__);
        $actionManager = ActionManager::getInstance();

        //wykonanie wskazanej metody (akcji) na obiekcie zbioru danych i zwrócenie ewentualnych błędów
        $errors = $actionManager->action();

        //zwracanie danych
        $result = $actionManager->getResultData();

        if (count($errors))
            $result['errors'] = $errors;

        //zwraca wynik do przeglądarki
        echo \Zend_Json::encode($result);
    }

    /**
     * Czyści dane zapisane w sesji pod kluczem db
     * 
     * @return void
     */
    public function clearSessionAction() {
        $this->_helper->viewRenderer->setNoRender();
        \Zend_Session::namespaceUnset('db');
        echo 'Sesja dostępu do zbiorów danych została wyczyszczona';
    }

    /**
     * Ładuje pliki na serwer
     * 
     * @return void
     */
    public function uploadAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $param = $this->_getAllParams();
        if (isset($param['datasource'])) {
            $dbs = new \Zend_Session_Namespace('db');
            if (isset($dbs->datasource)
                    && array_key_exists($param['datasource'], $dbs->datasource)) {
                $dataSource = unserialize($dbs->datasource[$param['datasource']]);
                $dataSet = $dataSource->getDataSet();
            }
        }

        $options = array(
            'param_name' => $param['name']
        );
        \Blueimp\Upload\Handler::$uploadDir = urldecode($param['uploaddir']);
        $upload_handler = new \Blueimp\Upload\Handler($options);
        //nazwa pliku zbudowana z identyfikatora zbioru danych, nazwy pola i czasu
        $upload_handler->changed_name = $param['datasource'] . '_' . $param['datafield'] . '_' . time();

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                $upload_handler->get();
                break;
            case 'POST':
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $upload_handler->delete();
                } else {
                    $upload_handler->post();
                }
                break;
            case 'DELETE':
                $upload_handler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
    }

}