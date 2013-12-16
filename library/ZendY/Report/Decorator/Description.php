<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Report\Decorator;

/**
 * Dekorator opisu raportu
 * 
 * @author Piotr Zając
 */
class Description extends Base {

    /**
     * Whether or not to escape the description
     * 
     * @var bool
     */
    protected $_escape;

    /**
     * Default placement: append
     * 
     * @var string
     */
    protected $_placement = 'APPEND';

    /**
     * HTML tag with which to surround description
     * 
     * @var string
     */
    protected $_tag;

    /**
     * Set HTML tag with which to surround description
     *
     * @param  string $tag
     * @return \ZendY\Report\Decorator\Description
     */
    public function setTag($tag) {
        $this->_tag = (string) $tag;
        return $this;
    }

    /**
     * Get HTML tag, if any, with which to surround description
     *
     * @return string
     */
    public function getTag() {
        if (null === $this->_tag) {
            $tag = $this->getOption('tag');
            if (null !== $tag) {
                $this->removeOption('tag');
            } else {
                $tag = 'p';
            }

            $this->setTag($tag);
            return $tag;
        }

        return $this->_tag;
    }

    /**
     * Get class with which to define description
     *
     * Defaults to 'hint'
     *
     * @return string
     */
    public function getClass() {
        $class = $this->getOption('class');
        if (null === $class) {
            $class = 'hint';
            $this->setOption('class', $class);
        }

        return $class;
    }

    /**
     * Set whether or not to escape description
     *
     * @param  bool $flag
     * @return \ZendY\Report\Decorator\Description
     */
    public function setEscape($flag) {
        $this->_escape = (bool) $flag;
        return $this;
    }

    /**
     * Get escape flag
     *
     * @return bool
     */
    public function getEscape() {
        if (null === $this->_escape) {
            if (null !== ($escape = $this->getOption('escape'))) {
                $this->setEscape($escape);
                $this->removeOption('escape');
            } else {
                $this->setEscape(true);
            }
        }

        return $this->_escape;
    }

    /**
     * Render a description
     *
     * @param  string $content
     * @return string
     */
    public function render($content) {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
            return $content;
        }

        $description = $element->getDescription();
        $description = trim($description);

        if (!empty($description) && (null !== ($translator = $element->getTranslator()))) {
            $description = $translator->translate($description);
        }

        if (empty($description)) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag = $this->getTag();
        $class = $this->getClass();
        $escape = $this->getEscape();

        $options = $this->getOptions();

        if ($escape) {
            $description = $view->escape($description);
        }

        if (!empty($tag)) {
            $options['tag'] = $tag;
            $decorator = new HtmlTag($options);
            $description = $decorator->render($description);
        }

        switch ($placement) {
            case self::PREPEND:
                return $description . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $description;
        }
    }

}
