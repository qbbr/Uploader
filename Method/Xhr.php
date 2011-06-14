<?php
/**
 * Xhr
 *
 * @package Q_Uploader
 * @author Sokolov Innokenty, <sokolov.innokenty@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @copyright Copyright (c) 2011, qbbr
 */
class Q_Uploader_Method_Xhr extends Q_Uploader_Method_Abstract
{
    public function getName()
    {
        if (isset($_GET[$this->_name])) {
            return $_GET[$this->_name];
        } else {
            throw new Q_Uploader_Method_Exception('Could not get file name');
        }
    }

    public function getSize()
    {
        if (isset($_SERVER['CONTENT_LENGTH'])){
            return (integer) $_SERVER['CONTENT_LENGTH'];
        } else {
            throw new Q_Uploader_Method_Exception('Could not get file size');
        }
    }

    public function save($path)
    {
        $input = fopen('php://input', 'r');

        $temp = tmpfile();

        $realSize = stream_copy_to_stream($input, $temp);

        fclose($input);

        if ($realSize != $this->getSize()) {
            throw new Q_Uploader_Method_Exception('Content size != real size');
        }

        $target = fopen($path, 'w');
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
}