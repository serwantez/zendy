<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */
require_once 'Zend/Validate/Db/NoRecordExists.php';

/**
 * Sprawdza czy podany adres e-mail nie był już rejestrowany w bazie
 */
class ZendY_Validate_NoEmailExists extends Zend_Validate_Db_NoRecordExists {
    /**
     * Error constants
     */

    const ERROR_RECORD_FOUND = 'recordFound';

    /**
     * Message templates
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERROR_RECORD_FOUND => "User with given e-mail already exists"
    );

}
