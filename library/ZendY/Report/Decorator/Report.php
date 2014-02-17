<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Decorator;

/**
 * Renderuje obiekt klasy ZendY\Report
 * 
 * @author Piotr Zając
 */
class Report extends Base {

    /**
     * Domyślny pomocnik widoku
     * 
     * @var string
     */
    protected $_helper = 'report';

    /**
     * Ustawia pomocnika widoku do wyrenderowania raportu
     * 
     * @param string $helper
     * @return \ZendY\Report\Decorator\Report
     */
    public function setHelper($helper) {
        $this->_helper = (string) $helper;
        return $this;
    }

    /**
     * Zwraca pomocnika widoku do wyrenderowania raportu
     *
     * @return string
     */
    public function getHelper() {
        if (null !== ($helper = $this->getOption('helper'))) {
            $this->setHelper($helper);
            $this->removeOption('helper');
        }
        return $this->_helper;
    }

    /**
     * Zwraca opcje dekoratora
     *
     * @return array
     */
    public function getOptions() {
        if (null !== ($element = $this->getElement())) {
            if ($element instanceof \ZendY\Report) {
                foreach ($element->getAttribs() as $key => $value) {
                    $this->setOption($key, $value);
                }
            }
        }
        return $this->_options;
    }

    /**
     * Renderuje raport
     *
     * @param  string $content
     * @return string
     */
    public function render($content) {
        $report = $this->getElement();
        $view = $report->getView();
        if (null === $view) {
            return $content;
        }

        $helper = $this->getHelper();
        $attribs = $this->getOptions();
        $name = $report->getFullyQualifiedName();
        $attribs['id'] = $report->getName();
        return $view->$helper($name, $attribs, $content);
    }

}
