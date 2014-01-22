<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

/**
 * Helper to generate a "checkbox" element
 */
class Checkbox extends Widget {

    /**
     * Default checked/unchecked options
     * @var array
     */
    protected static $_defaultCheckedOptions = array(
        'checkedValue' => '1',
        'uncheckedValue' => '0'
    );

    /**
     * Generates a 'checkbox' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * @param mixed $value The element value.
     * @param array $attribs Attributes for the element tag.
     * @return string The element XHTML.
     */
    public function checkbox($name, $value = null
    , array $params = array()
    , $attribs = null
    , array $options = null) {

        $attribs = $this->_prepareAttributes($name, $value, $attribs);
        $params = $this->_prepareParams($name, $params);
        $info = $this->_getInfo($name, $value, $attribs, $options);
        extract($info); // name, id, value, attribs, options, listsep, disable

        $checked = false;
        if (isset($attribs['checked']) && $attribs['checked']) {
            $checked = true;
            unset($attribs['checked']);
        } elseif (isset($attribs['checked'])) {
            $checked = false;
            unset($attribs['checked']);
        }

        $options = self::determineCheckboxInfo($value, $checked, $options);

        // is the element disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // build the element
        $xhtml = '';
        if ((!$disable && !strstr($name, '[]'))
                && (empty($attribs['disableHidden']) || !$attribs['disableHidden'])
        ) {
            $xhtml = $this->_hidden($name, $options['uncheckedValue']);
        }

        if (array_key_exists('disableHidden', $attribs)) {
            unset($attribs['disableHidden']);
        }

        $attribs = $this->_extractAttributes($id, $attribs);

        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s</span>';

        $xhtml .= '<input type="checkbox"'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . ' value="' . $this->view->escape($options['checkedValue']) . '"'
                . $options['checkedString']
                . $disabled
                . $this->_htmlAttribs($attribs['inner'])
                . $this->getClosingBracket();

        $xhtml = sprintf($container, $xhtml);

        return $xhtml;
    }

    /**
     * Determine checkbox information
     *
     * @param  string $value
     * @param  bool $checked
     * @param  array|null $checkedOptions
     * @return array
     */
    public static function determineCheckboxInfo($value, $checked, array $checkedOptions = null) {
        // Checked/unchecked values
        $checkedValue = null;
        $uncheckedValue = null;
        if (is_array($checkedOptions)) {
            if (array_key_exists('checkedValue', $checkedOptions)) {
                $checkedValue = (string) $checkedOptions['checkedValue'];
                unset($checkedOptions['checkedValue']);
            }
            if (array_key_exists('uncheckedValue', $checkedOptions)) {
                $uncheckedValue = (string) $checkedOptions['uncheckedValue'];
                unset($checkedOptions['uncheckedValue']);
            }
            if (null === $checkedValue) {
                $checkedValue = (string) array_shift($checkedOptions);
            }
            if (null === $uncheckedValue) {
                $uncheckedValue = (string) array_shift($checkedOptions);
            }
        } elseif ($value !== null) {
            $uncheckedValue = self::$_defaultCheckedOptions['uncheckedValue'];
        } else {
            $checkedValue = self::$_defaultCheckedOptions['checkedValue'];
            $uncheckedValue = self::$_defaultCheckedOptions['uncheckedValue'];
        }

        // is the element checked?
        $checkedString = '';
        if ($checked || ((string) $value === $checkedValue)) {
            $checkedString = ' checked="checked"';
            $checked = true;
        } else {
            $checked = false;
        }

        // Checked value should be value if no checked options provided
        if ($checkedValue == null) {
            $checkedValue = $value;
        }

        return array(
            'checked' => $checked,
            'checkedString' => $checkedString,
            'checkedValue' => $checkedValue,
            'uncheckedValue' => $uncheckedValue,
        );
    }

}
