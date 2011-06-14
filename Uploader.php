<?php
/**
 * Uploader
 *
 * @package Q_Uploader
 * @author Sokolov Innokenty, <sokolov.innokenty@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @copyright Copyright (c) 2011, qbbr
 */
class Q_Uploader
{
    /**
     * @var Q_Uploader_Method_Abstract
     */
    protected $_file;
    protected $_allowedExtensions;
    protected $_sizeLimit = 10485760; // 10M

    public function __construct($name)
    {
        if (isset($_GET[$name])) {
            $this->_file = new Q_Uploader_Method_Xhr($name);
        } elseif (isset($_FILES[$name])) {
            $this->_file = new Q_Uploader_Method_FileForm($name);
        } else {
            throw new Q_Uploader_Exception('Could not find files for upload');
        }
    }

    public function setAllowedExtensions(array $extensions)
    {
        $this->_allowedExtensions = $extensions;
    }

    public function setSizeLimit($size)
    {
        $this->_sizeLimit = $size;

        $this->checkServerSettings();
    }

    public function saveTo($dir, $name = null)
    {
        $this->prepareDir($dir);

        $filePath = $dir . DIRECTORY_SEPARATOR . $name;

        if (file_exists($filePath)) {
            throw new Q_Uploader_Exception("File ({$filePath}) already exist");
        }

        $size = $this->_file->getSize();
        $originalName = $this->_file->getName();

        if ($size == 0) {
            throw new Q_Uploader_Exception("File ({$originalName}) is empty");
        }

        if ($size > $this->_sizeLimit) {
            throw new Q_Uploader_Exception("File ({$originalName}) is too large");
        }

        $pathinfo = pathinfo($originalName);
        $originalFileName = $pathinfo['filename'];
        $ext = strtolower($pathinfo['extension']);

        if (!empty($this->_allowedExtensions) && !in_array($ext, $this->_allowedExtensions)) {
            throw new Q_Uploader_Exception(sprintf("File has an invalid extension, it should be one of (%s)",
                                                   implode(', ', $this->_allowedExtensions)));
        }

        return $this->_file->save($filePath);
    }

    protected function prepareDir($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777)) {
                throw new Q_Uploader_Exception("Dir ({$dir}) not found");
            }
        }

        if (!is_writable($dir)) {
            if (!@chmod($dir, 0777)) {
                throw new Q_Uploader_Exception("Dir ({$dir}) is not writable");
            }
        }
    }

    protected function checkServerSettings()
    {
        if (
            $this->toBytes(ini_get('post_max_size')) < $this->_sizeLimit
            || $this->toBytes(ini_get('upload_max_filesize')) < $this->_sizeLimit
           ) {
            $size = max(1, $this->_sizeLimit / 1024 / 1024) . 'M';
            throw new Q_Uploader_Exception("Increase post_max_size and upload_max_filesize to {$size}");
        }
    }

    protected function toBytes($string)
    {
        $val = (integer) $string;

        switch (strtolower(substr($string, -1))) {
            case 'g':
                $val *= 1024;

            case 'm':
                $val *= 1024;

            case 'k':
                $val *= 1024;
        }

        return $val;
    }
}