<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\View\Helper;

use ZendY\Db\DataSource;

/**
 * Pomocnik do wygenerowania kontrolki graficznej DbImage
 *
 * @author Piotr Zając
 */
class DbImage extends Widget {

    /**
     * Generuje kod kontrolki DbImage
     * 
     * @param string $id
     * @param string|null $value
     * @param array|null $params
     * @param array|null $attribs
     * @return string
     */
    public function dbImage($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        $params = $this->_prepareParams($id, $params);
        $uploadDirectory = $params['uploadDirectory'];
        if (count($params) > 0) {
            $params = \ZendY\JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('dc["im"]["%s"] = new image("%s",%s);', $id, $id, $params);

        unset($attribs['id']);
        unset($attribs['name']);
        unset($attribs['value']);
        $fileAttribs['data-url'] = DataSource::$controller
                . DataSource::$uploadAction
                . '?name=' . $id . '-uploader'
                . '&uploaddir=' . urlencode($uploadDirectory)
                . '&datasource=' . $attribs['options']['datasource']
                . '&datafield=' . $attribs['options']['datafield'];
        unset($attribs['options']);
        if (array_key_exists('disabled', $attribs)) {
            $fileAttribs['disabled'] = $attribs['disabled'];
            unset($attribs['disabled']);
        }

        $attribs = $this->_extractAttributes($id, $attribs);

        $image = '<img id="' . $id . '-img"';
        $image .= $this->_htmlAttribs($attribs['inner']);
        $image .= $this->getClosingBracket();

        $fileAttribs['class'] = \ZendY\Css::IMAGE_UPLOAD;
        $fileAttribs['value'] = $value;
        //$fileAttribs['multi'] = 'multi';

        $html = '<span id="' . $id . '-img-a" href="#"'
                . $this->_htmlAttribs($attribs['outer'])
                . '>' . $image . ' ' . $this->view->formFile($id . "-uploader", $fileAttribs) . '</span>';

        $this->view->headLink()->appendStylesheet($this->view->host . '/library/components/image/image.css');

        $this->jquery->addJavascriptFile($this->view->host . '/library/components/fileupload/js/jquery.fileupload.js');
        $this->jquery->addJavascriptFile($this->view->host . '/library/components/image/image.js');
        if (\Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) {
            $html .= '<script>' . $js . '</script>';
        } else {
            $this->jquery->addOnLoad($js);
        }

        return $html;
    }

}