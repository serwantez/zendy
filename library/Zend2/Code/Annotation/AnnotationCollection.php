<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend2\Code\Annotation;

use ArrayObject;

class AnnotationCollection extends ArrayObject
{
    /**
     * Checks if the collection has annotations for a class
     *
     * @param  string $class
     * @return bool
     */
    public function hasAnnotation($class)
    {
        foreach ($this as $annotation) {
            if (get_class($annotation) == $class) {
                return true;
            }
        }

        return false;
    }
}
