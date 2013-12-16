<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use ZendY\Css;
use ZendY\Exception;
use Zend2\Code\Generator;

/**
 * Zbiór przechowujące dane o strukturze drzewiastej plików na dysku
 *
 * @author Piotr Zając
 */
class FilesTree extends ArraySet implements TreeSetInterface {
    /*
     * Akcje na zbiorze
     */

    const ACTION_INSERTDOCBLOCK = 'insertDocBlockAction';
    const ACTION_DELETE = 'deleteAction';
    const ACTION_CANCEL = 'cancelAction';
    const ACTION_DOWNLOAD = 'downloadAction';

    /**
     * Kolumny zbioru
     */
    const COL_ID = 'id';
    const COL_NAME = 'name';
    const COL_FILEPATH = 'filepath';
    const COL_LFT = 'lft';
    const COL_RGT = 'rgt';
    const COL_DEPTH = 'depth';

    /**
     * Nazwa pola przechowującego wartość "z lewej"
     * 
     * @var string
     */
    protected $_leftField = self::COL_LFT;

    /**
     * Nazwa pola przechowującego wartość "z prawej"
     * 
     * @var string
     */
    protected $_rightField = self::COL_RGT;

    /**
     * Nazwa pola przechowującego wartość "głębokości zagnieżdżenia"
     * 
     * @var string
     */
    protected $_depthField = self::COL_DEPTH;

    /**
     * Ścieżka katalogu
     * 
     * @var string
     */
    protected $_path;

    /**
     * Rejestruje akcje zbioru
     * 
     * @return \ZendY\Db\DataSet\FilesTree
     */
    protected function _registerActions() {
        parent::_registerActions();
        $this->_registerAction(
                self::ACTION_INSERTDOCBLOCK
                , self::ACTIONTYPE_STANDARD
                , null
                , 'DocBlock'
                , null
                , false
                , self::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_DELETE
                , self::ACTIONTYPE_CONFIRM
                , array('primary' => Css::ICON_TRASH)
                , 'Delete'
                , null
                , true
                , self::ACTION_PRIVILEGE_DELETE
        );
        $this->_registerAction(
                self::ACTION_CANCEL
                , self::ACTIONTYPE_STANDARD
                , array('primary' => Css::ICON_CANCEL)
                , 'Cancel'
                , null
                , false
                , self::ACTION_PRIVILEGE_EDIT
        );
        $this->_registerAction(
                self::ACTION_DOWNLOAD
                , self::ACTIONTYPE_GENERATEFILE
                , array('primary' => Css::ICON_DISK)
                , 'Download'
                , null
                , false
                , self::ACTION_PRIVILEGE_VIEW
        );
        return $this;
    }

    /**
     * Ustawia stan akcji, czyli informację czy dana akcja może zostać wykonana
     * 
     * @param array|null $params
     * @return \ZendY\Db\DataSet\FilesTree
     */
    protected function _setActionState($params = array()) {
        parent::_setActionState($params);
        $cur = $this->getCurrent();
        $this->_navigator[self::ACTION_INSERTDOCBLOCK] = (
                $this->_state == self::STATE_VIEW
                && $this->_recordCount > 0
                && is_file($cur[self::COL_FILEPATH]));
        $this->_navigator[self::ACTION_DELETE] = ($this->_state == self::STATE_VIEW
                && $this->_recordCount > 0);
        $this->_navigator[self::ACTION_DOWNLOAD] = ($this->_state == self::STATE_VIEW
                && $this->_recordCount > 0);
        return $this;
    }

    /**
     * Akcja wstawiająca do pliku php blok ogólnego komentarza
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function insertDocBlockAction($params = array(), $compositePart = false) {
        $result = array();
        $cur = $this->getCurrent();
        if (is_file($cur[self::COL_FILEPATH])) {
            $file = Generator\FileGenerator::fromReflectedFileName($cur[self::COL_FILEPATH]);
            $nfile = new Generator\FileGenerator();
            $docBlock = $file->getDocBlock();
            $shortDescription = 'ZendY';
            $longDescription = '';
            $copyright = new Generator\DocBlock\Tag(array(
                        'name' => 'copyright',
                        'description' => 'E-FISH sp. z o.o. (http://www.efish.pl/)'
                    ));
            $tags = array(
                $copyright
            );
            if (!isset($docBlock)) {
                $docBlock = new Generator\DocBlockGenerator($shortDescription, $longDescription, $tags);
                $nfile->setDocBlock($docBlock);
                $nfile->setFilename($cur[self::COL_FILEPATH]);
                $nfile->setNamespace($file->getNamespace());
                $nfile->setRequiredFiles($file->getRequiredFiles());
                $nfile->setUses($file->getUses());
                $nfile->write();
            }
        }
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Usuwa katalog z zawartością
     * 
     * @param string $dir
     * @return void
     */
    static public function rmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rmdir($dir . "/" . $object); else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * Usuwa bieżący plik lub katalog
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     * @throws Exception
     */
    public function deleteAction($params = array(), $compositePart = false) {
        $result = array();
        $cur = $this->getCurrent();
        if (is_dir($cur[self::COL_FILEPATH])) {
            self::rmdir($cur[self::COL_FILEPATH]);
        } elseif (is_file($cur[self::COL_FILEPATH])) {
            unlink($cur[self::COL_FILEPATH]);
        } else {
            throw new Exception('Unknown file type');
        }
        $this->setPath($this->_path);
        if ($this->_offset >= $this->_recordCount && $this->_recordCount > 0)
            $this->_offset--;
        $this->_state = self::STATE_VIEW;
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Anulowanie usuwania
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function cancelAction($params = array(), $compositePart = false) {
        $result = array();
        if (!$compositePart) {
            $this->_setActionState();
        }
        return $result;
    }

    /**
     * Pobieranie pliku
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function downloadAction($params = array(), $compositePart = false) {
        $cur = $this->getCurrent();
        if (is_file($cur[self::COL_FILEPATH])) {
            if (function_exists('mime_content_type')) {
                $mtype = mime_content_type($cur[self::COL_FILEPATH]);
            } else if (function_exists('finfo_file')) {
                $finfo = finfo_open(FILEINFO_MIME);
                $mtype = finfo_file($finfo, $cur[self::COL_FILEPATH]);
                finfo_close($finfo);
            } else {
                $mtype = 'text/plain';
            }
            $pathParts = pathinfo($cur[self::COL_FILEPATH]);
            $response = \Zend_Controller_Front::getInstance()->getResponse();
            $response
                    ->setHeader('Content-Type', $mtype . '; charset=UTF-8')
                    ->setHeader('Content-Disposition', sprintf('attachment; filename="%s"', $pathParts["basename"]))
                    ->sendResponse();
            readfile($cur[self::COL_FILEPATH]);
        } else {
            
        }
        exit;
    }

    /**
     * Akcja odświeżająca dane
     * 
     * @param array|null $params
     * @param bool|null $compositePart
     * @return array
     */
    public function refreshAction($params = array(), $compositePart = false) {
        $this->setPath($this->_path);
        return parent::refreshAction($params, $compositePart);
    }

    /**
     * Ustawia pole przechowujące wartość "z lewej"
     *
     * @param string $left
     * @return \ZendY\Db\DataSet\FilesTree
     */
    public function setLeftField($left) {
        $this->_leftField = (string) $left;
        return $this;
    }

    /**
     * Ustawia pole przechowujące wartość "z prawej"
     *
     * @param string $right
     * @return \ZendY\Db\DataSet\FilesTree
     */
    public function setRightField($right) {
        $this->_rightField = (string) $right;
        return $this;
    }

    /**
     * Zwraca pole przechowujące wartość "z lewej"
     * 
     * @return string
     */
    public function getLeftField() {
        return $this->_leftField;
    }

    /**
     * Zwraca pole przechowujące wartość "z prawej"
     * 
     * @return string
     */
    public function getRightField() {
        return $this->_rightField;
    }

    /**
     * Ustawia pole przechowujące wartość "głębokości zagnieżdżenia"
     *
     * @param string $depth
     * @return \ZendY\Db\DataSet\FilesTree
     */
    public function setDepthField($depth) {
        $this->_depthField = (string) $depth;
        return $this;
    }

    /**
     * Zwraca pole przechowujące wartość "głębokości zagnieżdżenia"
     * 
     * @return string
     */
    public function getDepthField() {
        return $this->_depthField;
    }

    /**
     * Ustawia ścieżkę katalogu
     * 
     * @param string $path
     * @return \ZendY\Db\DataSet\FilesTree
     */
    public function setPath($path) {
        $this->_path = $path;
        $this->_data = array();
        $this->rec_scandir($path);
        $this->_recordCount = $this->_count();
        return $this;
    }

    /**
     * Zwraca ścieżkę katalogu
     * 
     * @return string
     */
    public function getPath() {
        return $this->_path;
    }

    /**
     * Przekształca ścieżkę pliku w identyfikator
     * 
     * @param string $path
     * @return string
     */
    public function pathToId($path) {
        $result = str_replace('/', '_', $path);
        $result = str_replace('.._library_', '', $result);
        return $result;
    }

    /**
     * Znajduje w podanym katalogu pliki i podkatalogi
     * i zapisuje do zmiennej tablicowej
     * 
     * @param string $dir
     * @param int $depth
     * @param int $left
     * @return int
     */
    protected function rec_scandir($dir, $depth = 0, $left = 1) {
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != ".." && $file != ".") {
                    if (is_dir($dir . "/" . $file)) {
                        $i = count($this->_data);
                        $this->_data[] = array(
                            self::COL_ID => $this->pathToId($dir . "/" . $file),
                            self::COL_NAME => $file,
                            self::COL_FILEPATH => $dir . "/" . $file,
                            self::COL_DEPTH => $depth,
                            self::COL_LFT => $left
                        );
                        $left++;
                        $depth++;
                        $left = $this->rec_scandir($dir . "/" . $file, $depth, $left);
                        $depth--;
                        $this->_data[$i][self::COL_RGT] = $left;
                        $left++;
                    } else {
                        $i = count($this->_data);
                        $this->_data[] = array(
                            self::COL_ID => $this->pathToId($dir . "/" . $file),
                            self::COL_NAME => $file,
                            self::COL_FILEPATH => $dir . "/" . $file,
                            self::COL_DEPTH => $depth,
                            self::COL_LFT => $left,
                            self::COL_RGT => $left + 1
                        );
                        $left += 2;
                    }
                }
            }
            closedir($handle);
            return $left;
        }
    }

}