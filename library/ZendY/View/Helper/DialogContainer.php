<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

require_once "ZendX/JQuery/View/Helper/DialogContainer.php";

/**
 * Pomocnik do wygenerowania okna dialogowego
 */
class DialogContainer extends \ZendX_JQuery_View_Helper_DialogContainer {

    /**
     * Create a jQuery UI Dialog filled with the given content
     *
     * @link   http://docs.jquery.com/UI/Dialog
     * @param  string $id
     * @param  string $content
     * @param  array $params
     * @param  array $attribs
     * @return string
     */
    public function dialogContainer($id, $content, $params = array(), $attribs = array()) {
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/dialog/jquery.ui.dialog.css');
        return parent::dialogContainer($id, $content, $params, $attribs);
    }

}