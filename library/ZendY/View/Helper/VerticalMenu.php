<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

require_once 'Zend/View/Helper/Navigation/Menu.php';

/**
 * Pomocnik do wygenerowania menu z ikonami
 *
 * @author Piotr Zając
 */
class VerticalMenu extends \Zend_View_Helper_Navigation_Menu {

    /**
     * Katalog ikon
     * 
     * @var string 
     */
    protected $_iconPath = '/images/icons';

    /**
     * Znacznik (x)html dla ikon
     * 
     * @var string 
     */
    protected $_iconTag = 'span';

    /**
     * Klasa css dla bieżącej strony
     * 
     * @var string
     */
    protected $_activeClass = 'active';

    /**
     * Generuje kod JS menu
     * 
     * @param string $id
     * @param array|null $params
     * @return \ZendY\View\Helper\IconMenu 
     */
    public function setIconMenu($id, array $params = array()) {

        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }


        $js = sprintf('%s("#%s ul.navigation").menu(%s);'
                , \ZendX_JQuery_View_Helper_JQuery::getJQueryHandler()
                , $id, $params);
        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/menu/jquery.ui.menu.css');
        $this->view->jQuery()->addOnLoad($js);
        return $this;
    }

    /**
     * Ustawia kontener nawigacji
     * 
     * @param \Zend_Navigation_Container $container
     * @return \ZendY\View\Helper\IconMenu 
     */
    public function verticalMenu(\Zend_Navigation_Container $container = null) {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }

    /**
     * Ustawia znacznik (x)html ikony
     * 
     * @param string $iconTag
     * @return \ZendY\View\Helper\IconMenu 
     */
    public function setIconTag($iconTag) {
        if (is_string($iconTag)) {
            $this->_iconTag = $iconTag;
        }

        return $this;
    }

    /**
     * Renderuje kod html
     * 
     * @param \Zend_Navigation_Page $page
     * @return string 
     */
    public function htmlify(\Zend_Navigation_Page $page) {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for element
        $attribs = array(
            'id' => $page->getId(),
            'title' => $title,
                /* 'class' => $page->getClass() */
        );
        if ($page->isActive(true)) {
            $attribs['class'] = $this->_activeClass;
        }

        // does page have a href?
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href'] = $this->view->baseUrl($href);
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        // dodanie ikonki
        if (null !== $page->icon) {
            $icon = '<img src="' . $this->_iconPath . '/' . $page->icon . '" alt="" /> ';
        } elseif (null !== $page->class && $page->class <> '') {
            $icon = '<' . $this->_iconTag . ' class="'
                    . \ZendY\Css::MENU_ICON_LEFT . ' '
                    . \ZendY\Css::ICON . ' '
                    . $page->class . '" />
                </' . $this->_iconTag . '> ';
        } else {
            $icon = '';
        }

        return '<' . $element . $this->_htmlAttribs($attribs) . '>'
                . $icon
                . $this->view->escape($label)
                . '</' . $element . '>';
    }

    /**
     * Ustawia klasę css dla bieżącej strony
     * 
     * @param string $class
     * @return \ZendY\View\Helper\VerticalMenu
     */
    public function setActiveClass($class) {
        $this->_activeClass = $class;
        return $this;
    }

}
