<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */
require_once 'Zend/Validate/Db/NoRecordExists.php';

/**
 * Sprawdza czy podany login nie jest już zajęty
 */
class ZendY_Validate_NoLoginExists extends Zend_Validate_Db_NoRecordExists {
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
        self::ERROR_RECORD_FOUND => "A user with this name already exists"
    );

}
