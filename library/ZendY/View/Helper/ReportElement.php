<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

require_once 'Zend/View/Helper/HtmlElement.php';

use ZendY\Exception;

/**
 * Bazowy pomocnik do wygenerowania elementów raportu
 * 
 * @author Piotr Zając
 */
abstract class ReportElement extends \Zend_View_Helper_HtmlElement {

    /**
     * Translator
     * 
     * @var \Zend_Translate_Adapter|null
     */
    protected $_translator;

    /**
     * Get translator
     *
     * @return \Zend_Translate_Adapter|null
     */
    public function getTranslator() {
        return $this->_translator;
    }

    /**
     * Set translator
     *
     * @param  \Zend_Translate|\Zend_Translate_Adapter|null $translator
     * @return \ZendY\View\Helper\ReportElement
     */
    public function setTranslator($translator = null) {
        if (null === $translator) {
            $this->_translator = null;
        } elseif ($translator instanceof \Zend_Translate_Adapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof \Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        } else {
            $e = new Exception('Invalid translator specified');
            $e->setView($this->view);
            throw $e;
        }
        return $this;
    }

    /**
     * Converts parameter arguments to an element info array.
     * 
     * @param string $name
     * @param mixed|null $value
     * @param array|null $attribs
     * @param array|null $options
     * @param string|null $listsep
     * @return string
     */
    protected function _getInfo($name, $value = null, $attribs = null, $options = null, $listsep = null
    ) {
        $info = array(
            'name' => is_array($name) ? '' : $name,
            'id' => is_array($name) ? '' : $name,
            'value' => $value,
            'attribs' => $attribs,
            'options' => $options,
            'listsep' => $listsep,
            'disable' => false,
            'escape' => true,
        );

        // override with named args
        if (is_array($name)) {
            // only set keys that are already in info
            foreach ($info as $key => $val) {
                if (isset($name[$key])) {
                    $info[$key] = $name[$key];
                }
            }

            // If all helper options are passed as an array, attribs may have
            // been as well
            if (null === $attribs) {
                $attribs = $info['attribs'];
            }
        }

        $attribs = (array) $attribs;

        // Normalize readonly tag
        if (array_key_exists('readonly', $attribs)) {
            $attribs['readonly'] = 'readonly';
        }

        // Disable attribute
        if (array_key_exists('disable', $attribs)) {
            if (is_scalar($attribs['disable'])) {
                // disable the element
                $info['disable'] = (bool) $attribs['disable'];
            } else if (is_array($attribs['disable'])) {
                $info['disable'] = $attribs['disable'];
            }
        }

        // Set ID for element
        if (array_key_exists('id', $attribs)) {
            $info['id'] = (string) $attribs['id'];
        } else if ('' !== $info['name']) {
            $info['id'] = trim(strtr($info['name'], array('[' => '-', ']' => '')), '-');
        }

        // Remove NULL name attribute override
        if (array_key_exists('name', $attribs) && is_null($attribs['name'])) {
            unset($attribs['name']);
        }

        // Override name in info if specified in attribs
        if (array_key_exists('name', $attribs) && $attribs['name'] != $info['name']) {
            $info['name'] = $attribs['name'];
        }

        // Determine escaping from attributes
        if (array_key_exists('escape', $attribs)) {
            $info['escape'] = (bool) $attribs['escape'];
        }

        // Determine listsetp from attributes
        if (array_key_exists('listsep', $attribs)) {
            $info['listsep'] = (string) $attribs['listsep'];
        }

        // Remove attribs that might overwrite the other keys. We do this LAST
        // because we needed the other attribs values earlier.
        foreach ($info as $key => $val) {
            if (array_key_exists($key, $attribs)) {
                unset($attribs[$key]);
            }
        }
        $info['attribs'] = $attribs;

        // done!
        return $info;
    }

}
