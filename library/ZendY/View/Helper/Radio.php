<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania kontrolki opcji "radio"
 * 
 * @author Piotr Zając
 */
class Radio extends Widget {

    /**
     * Input type to use
     * 
     * @var string
     */
    protected $_inputType = 'radio';

    /**
     * Whether or not this element represents an array collection by default
     * 
     * @var bool
     */
    protected $_isArray = false;

    /**
     * Generates a set of radio button elements.
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The radio value to mark as 'checked'.
     *
     * @param array $options An array of key-value pairs where the array
     * key is the radio value, and the array value is the radio text.
     *
     * @param array|string $attribs Attributes added to each radio.
     *
     * @return string The radio buttons XHTML.
     */
    public function radio($name, $value = null, $params = null, $attribs = null, $options = null, $listsep = "<br />\n") {
        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $params = $this->_prepareParams($name, $params);
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable
        // retrieve attributes for labels (prefixed with 'label_' or 'label')
        $attribs = $this->_extractAttributes($attribs);
        $label_attribs = array();
        foreach ($attribs['inner'] as $key => $val) {
            $tmp = false;
            $keyLen = strlen($key);
            if ((6 < $keyLen) && (substr($key, 0, 6) == 'label_')) {
                $tmp = substr($key, 6);
            } elseif ((5 < $keyLen) && (substr($key, 0, 5) == 'label')) {
                $tmp = substr($key, 5);
            }

            if ($tmp) {
                // make sure first char is lowercase
                $tmp[0] = strtolower($tmp[0]);
                $label_attribs[$tmp] = $val;
                unset($attribs['inner'][$key]);
            }
        }

        $labelPlacement = 'append';
        foreach ($label_attribs as $key => $val) {
            switch (strtolower($key)) {
                case 'placement':
                    unset($label_attribs[$key]);
                    $val = strtolower($val);
                    if (in_array($val, array('prepend', 'append'))) {
                        $labelPlacement = $val;
                    }
                    break;
            }
        }

        // the radio button values and labels
        $options = (array) $options;

        // build the element
        $xhtml = '';
        $list = array();

        // should the name affect an array collection?
        $name = $this->view->escape($name);
        if ($this->_isArray && ('[]' != substr($name, -2))) {
            $name .= '[]';
        }

        // ensure value is an array to allow matching multiple times
        $value = (array) $value;

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof \Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag = '>';
        }

        // Set up the filter - Alnum + hyphen + underscore
        require_once 'Zend/Filter/PregReplace.php';
        $pattern = @preg_match('/\pL/u', 'a') ? '/[^\p{L}\p{N}\-\_]/u' : '/[^a-zA-Z0-9\-\_]/';
        $filter = new \Zend_Filter_PregReplace($pattern, "");

        // add radio buttons to the list.
        foreach ($options as $opt_value => $opt_label) {

            // Should the label be escaped?
            if ($escape) {
                $opt_label = $this->view->escape($opt_label);
            }

            // is it disabled?
            $disabled = '';
            if (true === $disable) {
                $disabled = ' disabled="disabled"';
            } elseif (is_array($disable) && in_array($opt_value, $disable)) {
                $disabled = ' disabled="disabled"';
            }

            // is it checked?
            $checked = '';
            if (in_array($opt_value, $value)) {
                $checked = ' checked="checked"';
            }

            if (count($list) == 0)
                $optId = $id; else
                $optId = $id . '-' . $filter->filter($opt_value);

            $label = '<label ' . $this->_htmlAttribs($label_attribs) . ' for="' . $optId . '">'
                    . $opt_label
                    . '</label>';

            // Wrap the radios in labels
            $radio = '<input type="' . $this->_inputType . '"'
                    . ' name="' . $name . '"'
                    . ' id="' . $optId . '"'
                    . ' value="' . $this->view->escape($opt_value) . '"'
                    . $checked
                    . $disabled
                    . $this->_htmlAttribs($attribs['inner'])
                    . $endTag;

            if ('prepend' == $labelPlacement) {
                $radio = $label . $radio;
            } elseif ('append' == $labelPlacement) {
                $radio .= $label;
            }

            // add to the array of radio buttons
            $list[] = $radio;
        }

        // done!
        $xhtml .= implode($listsep, $list);

        $attribs['outer']['id'] = $id . '-container';

        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</div>';
        
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/radio/radio.css');

        $xhtml = sprintf($container, $xhtml);
        
        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["ra"]["%s"] = new radio("%s",%s);', $id, $id, $params);        
        
        $this->jquery->addJavascriptFile($this->view->host .
                '/library/components/radio/radio.js');
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $xhtml .= '<script>'. $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }        

        return $xhtml;
    }

}
