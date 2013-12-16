<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Decorator;

require_once "ZendX/JQuery/Form/Decorator/DialogContainer.php";

/**
 * Dekorator formularza dla pomocnika widoku panelu pudełkowego
 * 
 * @author Piotr Zając
 */
class Box extends \ZendX_JQuery_Form_Decorator_DialogContainer {

    /**
     * Pomocnik widoku
     * 
     * @var string
     */
    protected $_helper = "boxContainer";

    /**
     * Nadpisanie metody renderującej z powodu braku tłumaczenia tytułu okna
     * 
     * @param string $content
     * @return string
     */
    public function render($content) {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
            return $content;
        }

        $jQueryParams = $this->getJQueryParams();
        if (isset($jQueryParams['title']) && !empty($jQueryParams['title'])) {
            if (null !== ($translator = $element->getTranslator())) {
                $jQueryParams['title'] = $translator->translate($jQueryParams['title']);
            }
        }
        $attribs = $this->getOptions();

        $helper = $this->getHelper();
        $id = $element->getId() . '-container';

        return $view->$helper($id, $content, $jQueryParams, $attribs);
    }

}