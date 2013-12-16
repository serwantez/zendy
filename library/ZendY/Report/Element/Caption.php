<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Element;

/**
 * Element tekstowy raportu
 * 
 * @author Piotr Zając
 */
class Caption extends Xhtml {

    /**
     * Domyślny pomocnik widoku
     * 
     * @var string
     */
    public $helper = 'reportText';

    /**
     * Load default decorators
     * 
     * @return \ZendY\Report\Element\Caption
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper');
        }
        return $this;
    }

}
