<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet;

use Zend2\Code\Generator;

/**
 * Zbiór przechowujący dane o strukturze pliku php
 *
 * @author Piotr Zając
 */
class PhpFileTree extends ArraySet implements TreeSetInterface {
    /**
     * Kolumny zbioru
     */

    const COL_ID = 'id';
    const COL_PART = 'part';
    const COL_NAME = 'name';
    const COL_LFT = 'lft';
    const COL_RGT = 'rgt';
    const COL_DEPTH = 'depth';
    const COL_ICON = 'icon';

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
     * Ścieżka pliku
     * 
     * @var string
     */
    protected $_fileName;

    /**
     * Ustawia pole przechowujące wartość "z lewej"
     *
     * @param string $left
     * @return \ZendY\Db\DataSet\PhpFileTree
     */
    public function setLeftField($left) {
        $this->_leftField = (string) $left;
        return $this;
    }

    /**
     * Ustawia pole przechowujące wartość "z prawej"
     *
     * @param string $right
     * @return \ZendY\Db\DataSet\PhpFileTree
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
     * @return \ZendY\Db\DataSet\PhpFileTree
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
     * Ustawia ścieżkę pliku
     * 
     * @param string $fileName
     * @return \ZendY\Db\DataSet\PhpFileTree
     */
    public function setFileName($fileName) {
        $this->_fileName = $fileName;
        $file = Generator\FileGenerator::fromReflectedFileName($fileName);
        $this->_buildFileStructure($file);
        return $this;
    }

    /**
     * Zwraca ścieżkę pliku
     * 
     * @return string
     */
    public function getFileName() {
        return $this->_fileName;
    }

    /**
     * Buduje strukturę pliku php
     * 
     * @param \Zend2\Code\Generator\FileGenerator $file
     */
    protected function _buildFileStructure(Generator\FileGenerator $file) {
        $docBlock = $file->getDocBlock();
        $id = 0;
        $left = 0;
        if (isset($docBlock)) {
            $left++;
            $id++;
            $this->_data[] = array(
                self::COL_ID => $id,
                self::COL_PART => 'docBlock',
                self::COL_NAME => $docBlock->getShortDescription(),
                self::COL_ICON => \ZendY\Css::ICON_ARROW1E,
                $this->getLeftField() => $left,
                $this->getRightField() => $left++,
                $this->getDepthField() => 1
            );
        }
        $namespace = $file->getNamespace();
        if (isset($namespace)) {
            $left++;
            $id++;
            $this->_data[] = array(
                self::COL_ID => $id,
                self::COL_PART => 'namespace',
                self::COL_NAME => $namespace,
                self::COL_ICON => \ZendY\Css::ICON_ARROW1E,
                $this->getLeftField() => $left,
                $this->getRightField() => $left++,
                $this->getDepthField() => 1
            );
        }
    }

}