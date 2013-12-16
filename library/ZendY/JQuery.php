<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

require_once 'ZendX/JQuery.php';

/**
 * Klasa globalna do obsługi biblioteki jQuery
 *
 * @author Piotr Zając
 */
class JQuery extends \ZendX_JQuery {
    /**
     * Zdarzenia w jQuery (JavaScript)
     */

    const EVENT_BLUR = 'blur';
    const EVENT_CHANGE = 'change';    
    const EVENT_CLICK = 'click';
    const EVENT_DBLCLICK = 'dblclick';
    const EVENT_FOCUS = 'focus';
    const EVENT_HOVER = 'hover';
    const EVENT_KEYDOWN = 'keydown';
    const EVENT_KEYPRESS = 'keypress';
    const EVENT_KEYUP = 'keyup';
    const EVENT_MOUSEDOWN = 'mousedown';
    const EVENT_MOUSEENTER = 'mouseenter';
    const EVENT_MOUSELEAVE = 'mouseleave';
    const EVENT_MOUSEMOVE = 'mousemove';
    const EVENT_MOUSEOUT = 'mouseout';
    const EVENT_MOUSEOVER = 'mouseover';
    const EVENT_MOUSEUP = 'mouseup';
    const EVENT_RESIZE = 'resize';
    const EVENT_SCROLL = 'scroll';
    const EVENT_SELECT = 'select';
    const EVENT_SUBMIT = 'submit';

    /**
     * Tworzy jQuerowy obiekt zdarzenia z podanych operacji
     * 
     * @param string $operation
     * @return \Zend_Json_Expr
     */
    public static function createJQueryObject($objectId) {
        $js = sprintf('%s("#%s")'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $objectId
        );
        return new \Zend_Json_Expr($js);
    }

    /**
     * Tworzy jQuerowy obiekt zdarzenia z podanych operacji
     * 
     * @param string $operation
     * @return \Zend_Json_Expr
     */
    public static function createJQueryEventObject($operations) {
        $js = 'function(event, ui) {' . PHP_EOL
                . $operations . PHP_EOL
                . '}';
        return new \Zend_Json_Expr($js);
    }

}