<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

require_once "Zend/Registry.php";

use ZendY\Exception;
use ZendY\Form\Element;

/**
 * Pomocnik do wygenerowania kontrolki DateTimePicker
 *
 * @author Piotr Zając
 */
class DateTimePicker extends Widget {

    /**
     * Tworzy kod kontrolki DateTimePicker
     *
     * @link   http://trentrichardson.com/examples/timepicker/
     * @param  string $id
     * @param  string|null $value
     * @param  array|null  $params jQuery Widget Parameters
     * @param  array|null  $attribs HTML Element Attributes
     * @return string
     */
    public function datetimePicker($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);
        $iconParams = $this->_cutIconParams($params);

        //język
        if (!isset($params['regional']))
            $params['regional'] = self::resolveZendLocaleToDatePickerRegional();

        //format daty
        $params['dateFormat'] = \ZendX_JQuery_View_Helper_DatePicker::resolveZendLocaleToDatePickerFormat($params['dateFormat']);

        $js[] = sprintf('%s("#%s").datetimepicker($.datepicker.regional[ "%s" ] );'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $attribs['id']
                , $params['regional']);
        $regional = $params['regional'];
        unset($params['regional']);

        $events = array('beforeShow', 'onClose', 'onSelect');
        foreach ($params as $key => $v) {
            if (in_array($key, $events)) {
                $params[$key] = 'function(){' . PHP_EOL . $v . PHP_EOL . '}' . PHP_EOL;
                $params[$key] = new \Zend_Json_Expr($params[$key]);
            }
        }

        $params = \ZendY\JQuery::encodeJson($params);

        $html = array();
        $js[] = sprintf('%s("#%s").datetimepicker("option",%s);'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $attribs['id']
                , $params);

        $js[] = sprintf('dc["dp"]["%s"] = new datepicker2("%s");', $attribs['id'], $attribs['id']);


        $this->jquery->addJavascriptFile($this->view->host . '/library/components/datepicker/datepicker2.js');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/datetimepicker/jquery-ui-timepicker-addon.js');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/datetimepicker/i18n/jquery-ui-timepicker-' . $regional . '.js');
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/datepicker/jquery.ui.datepicker.css');
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/datetimepicker/jquery-ui-timepicker-addon.css');
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/slider/jquery.ui.slider.css');
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html = array_merge($html, array('<script>'), $js, array('</script>'));
        } else {
            $this->jquery->addOnLoad(implode("\n", $js));
        }

        return $this->view->iconEdit($id, $value, $iconParams, $attribs) . (implode("\n", $html));
    }

    /**
     * Zwraca maskę na podstawie formatu DatePicker'a
     * 
     * @param string $format
     * @return string
     */
    public static function datePickerFormatToMask($format) {
        $dateFormat = array(
            'DD' => '99', 'D' => '99', 'dd' => '99', 'd' => '99',
            'MM' => '99', 'M' => '99', 'mm' => '99', 'm' => '99',
            'yy' => '9999', 'y' => '99'
        );

        $newFormat = "";
        $isText = false;
        $i = 0;
        while ($i < strlen($format)) {
            $chr = $format[$i];
            if ($chr == '"' || $chr == "'") {
                $isText = !$isText;
            }
            $replaced = false;
            if ($isText == false) {
                foreach ($dateFormat AS $zl => $jql) {
                    if (substr($format, $i, strlen($zl)) == $zl) {
                        $chr = $jql;
                        $i += strlen($zl);
                        $replaced = true;
                    }
                }
            }
            if ($replaced == false) {
                $i++;
            }
            $newFormat .= $chr;
        }

        return $newFormat;
    }

    /**
     * Zwraca język kontrolki na podstawie obiektu Zend_Locale
     * 
     * @return string
     * @throws Exception
     */
    public static function resolveZendLocaleToDatePickerRegional() {
        if (\Zend_Registry::isRegistered('Zend_Locale')) {
            $locale = \Zend_Registry::get('Zend_Locale');
            if (!($locale instanceof \Zend_Locale)) {
                require_once "ZendY/Exception.php";
                throw new Exception("Cannot resolve Zend Locale format by default, no application wide locale is set.");
            }
            $lang = $locale->getLanguage();
            if ($lang == 'en')
                return '';
            return $lang;
        } else
            return '';
    }

    /**
     * Wycina parametry dotyczące pola tekstowego z ikoną
     * 
     * @param array $params
     * @return array
     */
    protected function _cutIconParams(array &$params) {
        $iconParams = array();
        if (array_key_exists(Element\IconEdit::PARAM_ICON, $params)) {
            $iconParams[Element\IconEdit::PARAM_ICON] = $params[Element\IconEdit::PARAM_ICON];
            unset($params[Element\IconEdit::PARAM_ICON]);
        }
        if (array_key_exists(Element\IconEdit::PARAM_POSITION, $params)) {
            $iconParams[Element\IconEdit::PARAM_POSITION] = $params[Element\IconEdit::PARAM_POSITION];
            unset($params[Element\IconEdit::PARAM_POSITION]);
        }

        return $iconParams;
    }

}