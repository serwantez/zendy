<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Css;
use ZendY\Form\Element;

/**
 * Pomocnik do wygenerowania kontrolki kalendarza
 *
 * @author Piotr Zając
 */
class Calendar extends Widget {

    /**
     * Generuje kod kontrolki kalendarza
     * 
     * @param string $id
     * @param mixed|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @param array|null $options
     * @return string
     */
    public function calendar($id, $value = null, array $params = array(), array $attribs = array(), $options = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);

        if (isset($attribs['lists'])) {
            if (count($attribs['lists']) > 0)
                $lists = $attribs['lists'];
            unset($attribs['lists']);
        }

        $attribs = $this->_extractAttributes($id, $attribs);
        $container = '<span'
                . $this->_htmlAttribs($attribs['outer'])
                . '>%s%s</span>';

        $locale = \Zend_Registry::get('Zend_Locale');
        $dayNames = \Zend_Locale_Data::getList($locale, 'day', array('gregorian', 'format', 'wide'));
        $params['monthNames'] = \Zend_Locale_Data::getList($locale, 'month', array('gregorian', 'stand-alone', 'wide'));
        //jeśli ustawienia językowe nie posiadają opcji tłumaczenia "stand-alone"
        if (isset($params['monthNames'][1]) && $params['monthNames'][1] == 1)
            $params['monthNames'] = \Zend_Locale_Data::getList($locale, 'month', array('gregorian', 'format', 'wide'));
        $calendar = $this->_generateCalendar($id, $dayNames, $params);
        //przkeształcenie daty do formatu js
        $params[Element\Calendar::PARAM_CURRENT_DATE] = $params[Element\Calendar::PARAM_CURRENT_DATE]->toValue() * 1000;

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = "{}";
        }

        $html = sprintf($container, $this->view->formHidden($id, $value), $calendar);

        $js[] = sprintf('dc["ca"]["%s"] = new calendar("%s", %s);'
                , $id
                , $id
                , $params);
        if (isset($lists)) {
            $listNames = array();
            foreach ($lists as $list) {
                $listNames[] = $list['listSource']->getName();
            }
            $js[] = sprintf('dc["ca"]["%s"].setNavigating(%s, "%s")'
                    , $id
                    , \ZendY\JQuery::encodeJson($listNames)
                    , $lists['standard']['listSource']->getFormId());
        }

        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/calendar/calendar.css');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/calendar/calendar.js');
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>' . implode(self::EOL, $js) . '</script>';
        } else {
            $this->jquery->addOnLoad(implode(self::EOL, $js));
        }

        return $html;
    }

    /**
     * Funkcja pomocnicza generująca kod właściwy kalendarza
     * 
     * @param string $id
     * @param array $dayNames
     * @param array $params
     * @return string
     */
    protected function _generateCalendar($id, $dayNames, $params) {
        $html[] = sprintf('<div class="%s %s %s">
            <div class="%s">'
                , Css::CALENDAR_NAVI
                , Css::WIDGET_HEADER
                , Css::PADDING_ALL
                , Css::CALENDAR_NAVI_LEFT);
        $html[] = $this->view->button($id . '-button-prev', 'Previous', array(
            'text' => false,
            'label' => 'Previous',
            'icons' => array('primary' => Css::ICON_CARAT1W),
            'shortkey' => 'Ctrl+,'
                ));
        $html[] = $this->view->button($id . '-button-next', 'Next', array(
            'text' => false,
            'label' => 'Next',
            'icons' => array('primary' => Css::ICON_CARAT1E),
            'shortkey' => 'Ctrl+.'
                ));
        $html[] = sprintf('<div class="%s"><span></span></div>', Css::CALENDAR_RANGE);
        $html[] = sprintf('</div>
            <div class="%s">', Css::CALENDAR_NAVI_RIGHT);
        $html[] = $this->view->radioButton($id . '-button-range', $params[Element\Calendar::PARAM_RANGE]
                , array('text' => true)
                , array()
                , Element\Calendar::getRanges()
                , '');
        $html[] = '</div>
            </div>';

        $html[] = sprintf('<table class="%s %s">
            <thead>
            <tr>'
                , Css::CALENDAR_DAYNAMES
                , Css::WIDGET);
        foreach ($dayNames as $dayName) {
            $html[] = sprintf('<th class="%s">%s</th>', Css::WIDGET_HEADER, $dayName);
        }
        $html[] = '</tr>
            </thead>
            </table>';
        $html[] = sprintf('<div class="%s">
            <table>
            <tbody>'
                , Css::CALENDAR_BODY);
        $html[] = '</tbody>
            </table>
            </div>';
        return implode("\n", $html);
    }

}

