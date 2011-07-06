<?php
/**
 * Abstract
 *
 * @package Q_Uploader
 * @author Sokolov Innokenty, <sokolov.innokenty@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @copyright Copyright (c) 2011, qbbr
 */
abstract class Q_Uploader_Method_Abstract
{
    protected $_name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    abstract public function getName();

    abstract public function getSize();

    /**
     * @param string $path
     */
    abstract public function save($path, $n);
}
