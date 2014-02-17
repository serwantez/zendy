<?php

use ZendY\Db\DataSet\App\User;

/**
 * HelpController
 *
 * Kontroler help
 *
 * @author Piotr Zając
 *
 */
class HelpController extends Zend_Controller_Action {

    public function aboutAction() {
        $user = new User();
        $userData = $user->findBy(User::COL_ID, 3, TRUE);
        if (isset($userData[User::COL_USERNAME])) {
            $contact['name'] = $userData[User::COL_FIRSTNAME] . ' ' . $userData[User::COL_SURNAME];
            $contact['email'] = $userData[User::COL_EMAIL];
        } else {
            $contact = array(
                'name' => '',
                'email' => ''
            );
        }
        $this->view->contact = $contact;
    }

}

