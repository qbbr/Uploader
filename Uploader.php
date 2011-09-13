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
    protected $_sizeLimit = 1048576; // 1M
    protected $_originalFileName = false;
    protected $_fileName;
    protected $_uploadDir;
    protected $_isArray = false;

    protected $_errors = array(
        'File (%s) already exist',
        'File (%s) is empty',
        'File (%s) is too large',
        'File has an invalid extension, it should be one of (%s)'
    );

    protected $_imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

    /**
     * @throws Q_Uploader_Exception
     * @param string $name
     */
    public function __construct($name)
    {
        if (isset($_GET[$name])) {
            $this->_file = new Q_Uploader_Method_Xhr($name);
        } elseif (isset($_FILES[$name])) {
            $this->_file = new Q_Uploader_Method_FileForm($name);
            $this->_isArray = is_array($_FILES[$name]['name']);
        } else {
            throw new Q_Uploader_Exception('Could not find files for upload');
        }
    }

    /**
     * Set the allowable file extensions
     *
     * @param array $extensions
     * @return Q_Uploader
     */
    public function setAllowedExtensions(array $extensions)
    {
        $this->_allowedExtensions = $extensions;

        return $this;
    }

    /**
     * Set file size limit
     *
     * @param integer $size
     * @return Q_Uploader
     */
    public function setSizeLimit($size)
    {
        $this->_sizeLimit = $size;

        $this->checkServerSettings();

        return $this;
    }

    /**
     * Set upload directory
     *
     * @param string $dir
     * @return Q_Uploader
     */
    public function setUploadDir($dir)
    {
        $this->prepareDir($dir);

        $this->_uploadDir = $dir;

        return $this;
    }

    /**
     * Save original filename, not rename
     *
     * @param boolean $originalFileName
     * @return Q_Uploader
     */
    public function originalFileName($originalFileName = false)
    {
        $this->_originalFileName = $originalFileName;

        return $this;
    }

    /**
     * Set file name
     *
     * @param string $fileName
     * @return Q_Uploader
     */
    public function setFileName($fileName)
    {
        $this->_fileName = $fileName;

        return $this;
    }

    /**
     * Upload file to server
     *
     * @return array
     */
    public function upload()
    {
        $originalNames = $this->_file->getName();
        if (false === $this->_isArray) $originalNames = array($originalNames);

        $sizes = $this->_file->getSize();
        if (false === $this->_isArray) $sizes = array($sizes);

        $count = count($originalNames);

        $return = array();

        foreach ($originalNames as $i => $originalName) {
            if (empty($originalName)) continue;

            $size = $sizes[$i];

            $info = $this->uploadFile($originalName, $size, $i);

            if (false === $this->_isArray) return $info;

            $return []= $info;
        }

        return $return;
    }

    /**
     * @param string $originalName
     * @param integer $size
     * @param integer $i
     * @return array
     */
    protected function uploadFile($originalName, $size, $i)
    {
        $pathinfo = pathinfo($originalName);

        $extension = strtolower($pathinfo['extension']);

        //$filename = (true === $this->_originalFileName) ? $pathinfo['filename'] : md5(uniqid() . $originalName);

        $filename = (null === $this->_fileName)
                  ? (true === $this->_originalFileName) ? $pathinfo['filename'] : md5(uniqid() . $originalName)
                  : $this->_fileName . '_' . $i;

        $basename = $filename . '.' . $extension;
        $filePath = $this->_uploadDir . DIRECTORY_SEPARATOR . $basename;

        $errors = array();

        if (file_exists($filePath)) {
            $errors []= sprintf($this->_errors[0], $filePath);
        }

        if (0 === $size) {
            $errors []= sprintf($this->_errors[1], $originalName);
        }

        if ($size > $this->_sizeLimit) {
            $errors []= sprintf($this->_errors[2], $originalName);
        }

        if (!empty($this->_allowedExtensions) && !in_array($extension, $this->_allowedExtensions)) {
            $errors []= sprintf($this->_errors[3], implode(', ', $this->_allowedExtensions));
        }

        if (empty($errors)) {
            $this->_file->save($filePath, $i);
        }

        $info = array(
            'basename' => $basename,
            'filename' => $filename,
            'extension' => $extension,
            'size' => $size,
            'errors' => $errors
        );

        if (in_array($extension, $this->_imageExtensions)) {
            $imageInfo = getimagesize($filePath);

            $info['image'] = array(
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'type' => $imageInfo[2],
                'mime' => $imageInfo['mime']
            );
        }

        return $info;
    }

    /**
     * @throws Q_Uploader_Exception
     * @param string $dir
     */
    protected function prepareDir($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new Q_Uploader_Exception("Dir ({$dir}) not found");
            }
        }

        if (!is_writable($dir)) {
            if (!@chmod($dir, 0777)) {
                throw new Q_Uploader_Exception("Dir ({$dir}) is not writable");
            }
        }
    }

    /**
     * @throws Q_Uploader_Exception
     */
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

    /**
     * @param string $string
     * @return integer
     */
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
