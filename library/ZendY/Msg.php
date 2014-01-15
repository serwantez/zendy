<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

/**
 * Zarządzanie komunikatami programistycznymi
 *
 * @author Piotr Zając
 */
class Msg extends Object {
    /**
     * Komunikaty predefiniowane
     */

    const MSG_ACTION_CONFIRM = 'Are you sure you want to execute this action?';
    const MSG_ACTION_NO_ACTION = 'Action %s is not registered';
    const MSG_ACTION_NO_PERMISSION = 'You have not permissions to execute action %s';
    const MSG_AUTH_FAIL = 'Wrong login or password';
    const MSG_AUTH_LOGOUT = 'You have successfully logged out';
    const MSG_AUTH_CONFIRM_SENT = 'A confirmation link has been sent to your e-mail address';
    const MSG_AUTH_RECOVERY_SENT = 'A recovery link has been sent to your e-mail address';
    const MSG_AUTH_ACTIVATION_SUCCESSFUL = 'Activation was successful';
    const MSG_AUTH_NO_USER = "A user with this name doesn't exist";
    const MSG_AUTH_PASS_CHANGED = "Password has been changed";
    const MSG_AUTH_NO_MAIL = 'Unknown e-mail';
    const MSG_AUTH_GROUP_REGISTERED = "Group has been registered. Wait for activation, please";
    const MSG_AUTH_GROUP_ACTIVATED = "Confirmation of the group activation has been sent to registrator's e-mail address";
    const MSG_DATA_LOADING = 'Loading data';
    const MSG_FORM_VALIDATION_ERRORS = 'Form validation errors';
    const MSG_SINGLETON_CLONE = '%s class cannot be instantiated. Please use the method called getInstance.';
    const MSG_TRANSLATE_NO = 'No translate object in registry';

    /**
     * Predefiniowane treści wiadomości mailowych
     */
    const MAILSUBJECT_AUTH_REGISTER = '%s - Confirmation of registration';
    const MAIL_AUTH_REGISTER = 'Niniejszy adres e-mail został użyty przy rejestracji nowego konta w serwisie <b>%s</b>.<br /><br />
        Jeżeli nie rejestrowałeś(-aś) się, po prostu zignoruj tą wiadomość.<br /><br />
        Aby aktywować zarejestrowane konto kliknij w poniższy link:<br />
        <a href="http://%s/auth/activateuser?flag=%s"><b>Potwierdź rejestrację</b></a><br /><br />%s';
    const MAILSUBJECT_AUTH_PASS_RECOVERY = '%s - Recovery password';
    const MAIL_AUTH_PASS_RECOVERY = 'Niniejszy adres e-mail został użyty przy odzyskiwaniu hasła w serwisie <b>%s</b>.<br /><br />
        Jeżeli nie prosiłeś(-aś) o to, po prostu zignoruj tą wiadomość.<br /><br />
        Aby utworzyć nowe hasło kliknij w poniższy link:<br />
        <a href="http://%s/auth/newpassword?flag=%s"><b>Utwórz hasło</b></a><br /><br />%s';
    const MAILSUBJECT_AUTH_GROUP_ACTIVATION = '%s - Group activation';
    const MAIL_AUTH_GROUP_ACTIVATION = 'Informujemy, iż proces aktywacji grupy: %s przebiegł poprawnie.<br /><br />
        Niniejszy adres e-mail został użyty przy rejestracji grupy muzycznej w serwisie <b>%s</b>.<br />
        Jeżeli nie rejestrowałeś(-aś) się, po prostu zignoruj tą wiadomość.<br /><br />%s';

    /**
     * Tablica komunikatów
     * 
     * @var array
     */
    protected static $_messages = array();

    /**
     * Zwraca aktualny czas z dokładnością do mikrosekundy
     * 
     * @return float
     */
    private static function getMicrotime() {
        //pobiera unixowy znacznik czasu
        list($microsec, $sec) = explode(" ", microtime());
        return ((float) $microsec + (float) $sec);
    }

    /**
     * Dodaje komunikat
     * 
     * @param string $msg
     * @return void
     */
    public static function add($msg) {
        self::$_messages[] = array('msg' => $msg, 'time' => self::getMicrotime());
    }

    /**
     * Zwraca wszystkie komunikaty
     * 
     * @return array
     */
    public static function getMessages() {
        return self::$_messages;
    }

    /**
     * Wyświetla wszystkie komunikaty
     * 
     * @return void
     */
    public static function render() {
        print_r(self::$_messages);
    }

    /**
     * Zwraca najdłuższe operacje
     * 
     * @return array
     */
    public static function getLongest() {
        foreach (self::$_messages as $i => $msg) {
            if ($i > 0) {
                $dt[$i] = $msg['time'] - self::$_messages[$i - 1]['time'];
            }
            else
                $dt[$i] = 0;
        }
        $dt = array_filter($dt, function($var) {
                    return $var > 0.3;
                });
        return $dt;
    }

}