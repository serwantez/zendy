<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Decorator;

/**
 * Bazowy interfejs dekoratorów raportu
 */
interface BaseInterface {

    /**
     * Constructor
     *
     * Accept options during initialization.
     *
     * @param  array|\Zend_Config|null $options
     * @return void
     */
    public function __construct($options = null);

    /**
     * Set an element to decorate
     *
     * While the name is "setElement", a report decorator could decorate either
     * an element or a report object.
     *
     * @param  mixed $element
     * @return BaseInterface
     */
    public function setElement($element);

    /**
     * Retrieve current element
     *
     * @return mixed
     */
    public function getElement();

    /**
     * Set decorator options from an array
     *
     * @param  array $options
     * @return \ZendY\Report\Decorator\BaseInterface
     */
    public function setOptions(array $options);

    /**
     * Set decorator options from a config object
     *
     * @param  \Zend_Config $config
     * @return \ZendY\Report\Decorator\BaseInterface
     */
    public function setConfig(\Zend_Config $config);

    /**
     * Set a single option
     *
     * @param  string $key
     * @param  mixed $value
     * @return \ZendY\Report\Decorator\BaseInterface
     */
    public function setOption($key, $value);

    /**
     * Retrieve a single option
     *
     * @param  string $key
     * @return mixed
     */
    public function getOption($key);

    /**
     * Retrieve decorator options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Delete a single option
     *
     * @param  string $key
     * @return bool
     */
    public function removeOption($key);

    /**
     * Clear all options
     *
     * @return \ZendY\Report\Decorator\BaseInterface
     */
    public function clearOptions();

    /**
     * Render the element
     *
     * @param  string $content Content to decorate
     * @return string
     */
    public function render($content);
}
