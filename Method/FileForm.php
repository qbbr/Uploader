<?php
/**
 * FileForm
 *
 * @package Q_Uploader
 * @author Sokolov Innokenty, <sokolov.innokenty@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @copyright Copyright (c) 2011, qbbr
 */
class Q_Uploader_Method_FileForm extends Q_Uploader_Method_Abstract
{
    public function getName()
    {
        if (isset($_FILES[$this->_name]['name'])) {
            return $_FILES[$this->_name]['name'];
        } else {
            throw new Q_Uploader_Method_Exception('Could not get file name');
        }
    }

    public function getSize()
    {
        if (isset($_FILES[$this->_name]['size'])){
            return $_FILES[$this->_name]['size'];
        } else {
            throw new Q_Uploader_Method_Exception('Could not get file size');
        }
    }

    public function save($path, $n)
    {
        if (!is_array($_FILES[$this->_name]['tmp_name'])) {
            $_FILES[$this->_name]['tmp_name'] = array($_FILES[$this->_name]['tmp_name']);
        }

        if (!move_uploaded_file($_FILES[$this->_name]['tmp_name'][$n], $path)) {
            throw new Q_Uploader_Method_Exception("Could not save file to ({$path})");
        }

        return true;
    }
}
