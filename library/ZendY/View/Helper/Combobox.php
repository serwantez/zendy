<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Pomocnik do wygenerowania listy rozwijalnej
 * 
 * @author Piotr Zając
 */
class Combobox extends Widget {

    /**
     * Generuje kod listy rozwijalnej
     * 
     * @param string $name
     * @param mixed $value
     * @param array|null $params
     * @param array|null $attribs
     * @param array|null $options
     * @param string|null $listsep
     * @return string
     */
    public function combobox($name, $value = null, $params = null, $attribs = null, $options = null, $listsep = "<br />\n") {
        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        $params = $this->_prepareParams($name, $params);
//        print_r($value);
        extract($info); // name, id, value, attribs, options, listsep, disable
        // force $value to array so we can compare multiple values to multiple
        // options; also ensure it's a string for comparison purposes.
        $value = array_map('strval', (array) $value);

        // check if element may have multiple values
        $multiple = '';

        if (substr($name, -2) == '[]') {
            // multiple implied by the name
            $multiple = ' multiple="multiple"';
        }

        if (isset($attribs['multiple'])) {
            // Attribute set
            if ($attribs['multiple']) {
                // True attribute; set multiple attribute
                $multiple = ' multiple="multiple"';

                // Make sure name indicates multiple values are allowed
                if (!empty($multiple) && (substr($name, -2) != '[]')) {
                    $name .= '[]';
                }
            } else {
                // False attribute; ensure attribute not set
                $multiple = '';
            }
            unset($attribs['multiple']);
        }

        // now start building the XHTML.
        $disabled = '';
        if (true === $disable) {
            $disabled = ' disabled="disabled"';
        }

        $params['id'] = $id;
        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js[] = sprintf('dc["cb"]["%s"] = new comboBox("%s",%s);', $id, $id, $params);

        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/combo/combobox.css');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/combo/combobox.js');
        $this->jquery->addOnLoad(implode("\n", $js));


        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<div'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</div>';

        // Build the surrounding select element first.
        $xhtml = '<select'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $multiple
                . $disabled
                . $this->_htmlAttribs($attribs['inner'])
                . ">\n    ";

        // build the list of options
        $list = array();
        $translator = $this->getTranslator();

        foreach ((array) $options as $opt_value => $opt_label) {
            if (is_array($opt_label)) {
                $opt_disable = '';
                if (is_array($disable) && in_array($opt_value, $disable)) {
                    $opt_disable = ' disabled="disabled"';
                }
                if (null !== $translator) {
                    $opt_value = $translator->translate($opt_value);
                }
                $opt_id = ' id="' . $this->view->escape($id) . '-optgroup-'
                        . $this->view->escape($opt_value) . '"';
                $list[] = '<optgroup'
                        . $opt_disable
                        . $opt_id
                        . ' label="' . $this->view->escape($opt_value) . '">';
                foreach ($opt_label as $val => $lab) {
                    $list[] = $this->_build($val, $lab, $value, $disable);
                }
                $list[] = '</optgroup>';
            } else {
                $list[] = $this->_build($opt_value, $opt_label, $value, $disable);
            }
        }

        // add the options to the xhtml and close the select
        $xhtml .= implode("\n    ", $list) . "\n</select>";
        $xhtml = sprintf($container, $xhtml);

        return $xhtml;
    }

    /**
     * Builds the actual <option> tag
     *
     * @param string $value Options Value
     * @param string $label Options Label
     * @param array  $selected The option value(s) to mark as 'selected'
     * @param array|bool $disable Whether the select is disabled, or individual options are
     * @return string Option Tag XHTML
     */
    protected function _build($value, $label, $selected, $disable) {
        if (is_bool($disable)) {
            $disable = array();
        }

        $opt = '<option'
                . ' value="' . $this->view->escape($value) . '"'
                . ' label="' . $this->view->escape($label) . '"';

        // selected?
        if (in_array((string) $value, $selected)) {
            $opt .= ' selected="selected"';
        }

        // disabled?
        if (in_array($value, $disable)) {
            $opt .= ' disabled="disabled"';
        }

        $opt .= '>' . $this->view->escape($label) . "</option>";

        return $opt;
    }

}
