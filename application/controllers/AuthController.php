<?php

use ZendY\Db\DataSet\App\User;
use ZendY\Db\DataSet\App\UserRole;
use Application\Form;
use ZendY\Form\Container\Dialog;
use ZendY\JQuery;

class AuthController extends Zend_Controller_Action {

    protected $_host;

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOptions();
        if (array_key_exists('host', $options)) {
            $this->_host = $options['host'];
        } else {
            $this->_host = $_SERVER["HTTP_HOST"];
        }

        $messages = $this->_helper->flashMessenger->getMessages();
        if (isset($messages) && count($messages) > 0) {
            $messages = implode('<br />', $messages);
            $this->_helper->layout->getView()->dialogMessages = $this->view->dialogContainer(
                    'messager', $messages, array(
                Dialog::PARAM_MODAL => TRUE,
                Dialog::PARAM_RESIZABLE => FALSE,
                Dialog::PARAM_DRAGGABLE => FALSE,
                'title' => $this->view->translate('Message'),
                Dialog::PARAM_BUTTONS => array(
                    'Ok' => new Zend_Json_Expr(JQuery::createJQueryEventObject('$( this ).dialog( "close" );'))
                )
                    ));
        }
    }

    public function profileAction() {
        $form = new Form\Profile();
        $this->view->form = $form;
    }

    public function loginAction() {
        $form = new Form\Auth();
        $request = $this->getRequest();
        if ($request->isPost() && $request->getParam('login')) {
            if ($form->isValid($request->getPost())) {

                $adapter = User::createAuthAdapter();
                $adapter->setIdentity($form->getNestedValue('username'));
                $adapter->setCredential($form->getNestedValue('password'));

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);

                switch ($result->getCode()) {
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        //nieistniejąca tożsamość
                        //$form->getNestedElement('username')->addError("A user with this name doesn't exist");
                        $form->getNestedElement('password')->addError('Wrong login or password');
                        break;
                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        //niepoprawne hasło
                        $form->getNestedElement('password')->addError('Wrong login or password');
                        break;
                    case Zend_Auth_Result::SUCCESS:
                        $auth->getStorage()->write($adapter->getResultRowObject());
                        $this->_redirect('/auth/profile');
                        return;
                        break;
                    default :
                        break;
                }
            }
        }
        $this->view->form = $form;
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $request = $this->getRequest();
        $this->_helper->flashMessenger->addMessage($this->view->translate('You have successfully logged out'));
        $this->_redirect('/auth/login');
    }

    private function _sentRegistrationMail($email, $login) {
        $sender = new ZendY\Mail\Sender();
        $sender->setSubject('Potwierdzenie rejestracji');
        $sender->setTo($email);
        $message = 'Niniejszy adres e-mail został użyty przy rejestracji w serwisie testowym biblioteki <b>ZendY</b>.<br /><br />
                Jeżeli nie rejestrowałeś(-aś) się, po prostu zignoruj tą wiadomość.<br /><br />
                Aby aktywować zarejestrowane konto kliknij w poniższy link:<br />
                <a href="http://' . $this->_host .
                '/auth/activateuser?flag=' .
                hash(User::getCryptographicFunction(), $login) .
                '"><b>Potwierdź rejestrację</b></a><br /><br />';
        $sender->setMessage(wordwrap($message));
        $sender->setSentMessage('A confirmation link has been sent to your email address');
        return $sender->send();
    }

    private function _sentRecoveryMail($email, $login) {
        $sender = new ZendY\Mail\Sender();
        $sender->setSubject('Recovery password');
        $sender->setTo($email);
        $message = 'Niniejszy adres e-mail został użyty przy odzyskiwaniu hasła w serwisie testowym biblioteki <b>ZendY</b>.<br /><br />
                Jeżeli nie prosiłeś(-aś) o to, po prostu zignoruj tą wiadomość.<br /><br />
                Aby utworzyć nowe hasło kliknij w poniższy link:<br />
                <a href="http://' . $this->_host .
                '/auth/newpassword?flag=' .
                hash(User::getCryptographicFunction(), $login) .
                '"><b>Utwórz hasło</b></a><br /><br />';
        $sender->setMessage(wordwrap($message));
        $sender->setSentMessage('A recovery link has been sent to your email address');
        return $sender->send();
    }

    public function signupAction() {
        $form = new Form\Signup();
        $request = $this->getRequest();
        if ($request->isPost() && $request->getParam('signup')) {
            if ($form->isValid($request->getPost())) {
                $user = new User();
                $userRole = new UserRole();
                $login = $form->getNestedValue('username');
                $email = $form->getNestedValue('email');
                $password = $form->getNestedValue('password');
                $data = array(
                    User::COL_USERNAME => $login,
                    User::COL_PASSWORD => $password,
                    User::COL_EMAIL => $email,
                    User::COL_ACTIVE => 0
                );
                $userId = $user->addUser($data);
                $roleData = array(
                    UserRole::COL_USER_ID => $userId,
                    UserRole::COL_ROLE_ID => 3
                );
                $userRole->addUserRole($roleData);
                $message = $this->_sentRegistrationMail($email, $login);
                $this->_helper->flashMessenger->addMessage($this->view->translate($message));
                $this->_redirect('/auth/login');
            }
        }
        $this->view->form = $form;
    }

    public function activateuserAction() {
        $data = $this->_getAllParams();

        $mdl = new User();

        if ($mdl->activateUser($data["flag"])) {
            $message = "Activation was successful";
        } else {
            $message = "A user with this name doesn't exist";
        }
        $this->_helper->flashMessenger->addMessage($this->view->translate($message));

        $this->_redirect('/auth/login');
    }

    public function changepasswordAction() {
        $form = new Form\ChangePassword();
        $request = $this->getRequest();
        if ($request->isPost() && $request->getParam('changePassword')) {
            if ($form->isValid($request->getPost())) {
                $user = new User();
                $password = $form->getNestedValue('password');
                $auth = Zend_Auth::getInstance();
                $user->changePassword($password, $auth->getIdentity()->id);
                $message = "Password has been changed";
                $this->_helper->flashMessenger->addMessage($this->view->translate($message));
                $this->_redirect('/auth/profile');
            }
        }
        $this->view->form = $form;
    }

    public function recoverpasswordAction() {
        $form = new Form\RecoverPassword();
        $request = $this->getRequest();
        if ($request->isPost() && $request->getParam('recoverPassword')) {
            if ($form->isValid($request->getPost())) {
                $user = new User();
                $email = $form->getNestedValue('email');
                $filter = new \ZendY\Db\Filter();
                $filter->addFilter(User::COL_EMAIL, $email);
                $user->filterAction(array('filter' => $filter));
                if ($user->getRecordCount() > 0) {
                    $login = $user->fetchOne(User::COL_USERNAME);
                    $message = $this->_sentRecoveryMail($email, $login);
                    $this->_helper->flashMessenger->addMessage($this->view->translate($message));
                    $this->_redirect('/auth/login');
                } else {
                    $form->getNestedElement('email')->addError('Wrong email');
                }
            }
        }
        $this->view->form = $form;
    }

    public function newpasswordAction() {
        $form = new Form\ChangePassword();
        $request = $this->getRequest();
        if ($request->isPost() && $request->getParam('changePassword')) {
            if ($form->isValid($request->getPost())) {
                $user = new User();
                $password = $form->getNestedValue('password');
                if ($user->newPassword($password, $request->getParam('flag'))) {
                    $message = "Password has been changed";
                    $this->_helper->flashMessenger->addMessage($this->view->translate($message));
                    $this->_redirect('/auth/profile');
                }
            }
        }
        $this->view->form = $form;
    }

}

