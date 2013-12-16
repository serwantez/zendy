<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Mail;

use ZendY\Object;
use ZendY\Exception;

/**
 * Klasa do do wysyłania maila
 *
 * @author Michał Barczyk, Piotr Zając
 */
class Sender extends Object {

    /**
     * Licznik instancji
     * 
     * @var int
     */
    static protected $_count = 0;

    /**
     * Obiekt do wysyłania poczty
     * 
     * @var \Zend_Mail
     */
    protected $_mail;

    /**
     * Obiekt transportowy
     * 
     * @var \Zend_Mail_Transport_Smtp
     */
    protected $_sentMessage = 'E-mail has been sent';

    /**
     * Konstruktor
     * 
     * @param string|null $id
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);
        $this->_mail = new \Zend_Mail('UTF-8');
        $this->_mail->addHeader('MIME-Version', '1.0');
        $this->_mail->addHeader('Content-Transfer-Encoding', '8bit');
        $this->_mail->addHeader('X-Mailer:', 'PHP/' . phpversion());
    }

    /**
     * Wysyła maila
     * 
     * @return \Zend_Mail_Transport_Smtp|\ZendY\Exception
     */
    public function send() {
        try {
            $this->_mail->send();
            return $this->_sentMessage;
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Ustawia treść wiadomości
     * 
     * @param string $html
     * @return \ZendY\Mail\Sender
     */
    public function setMessage($html) {
        $this->_mail->setBodyHtml($html);
        return $this;
    }

    /**
     * Ustawia treść komunikatu potwierdzającego wysłanie maila
     * 
     * @param string $message
     * @return \ZendY\Mail\Sender
     */
    public function setSentMessage($message) {
        $this->_sentMessage = $message;
        return $this;
    }

    /**
     * Ustawia nadawcę wiadomości
     * 
     * @param string $email
     * @param string $name
     * @return \ZendY\Mail\Sender
     */
    public function setFrom($email, $name) {
        $this->_mail->setFrom($email, $name);
        return $this;
    }

    /**
     * Ustawia adresata wiadomości
     * 
     * @param string $receiver
     * @return \ZendY\Mail\Sender
     */
    public function setTo($receiver) {
        $this->_mail->addTo($receiver);
        return $this;
    }

    /**
     * Ustawia temat wiadomości
     * 
     * @param string $subject
     * @return \ZendY\Mail\Sender
     */
    public function setSubject($subject) {
        $this->_mail->setSubject($subject);
        return $this;
    }

}
