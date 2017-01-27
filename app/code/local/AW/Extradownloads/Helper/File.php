<?php

/**
 * Extradownloads File Helper
 */
class AW_Extradownloads_Helper_File extends Mage_Core_Helper_Abstract
{
    /**
     * Url Type indentifier
     */
    const EXTRA_TYPE_URL         = 'url';

    /**
     * File Type identifier
     */
    const EXTRA_TYPE_FILE        = 'file';

    /**
     * Checking file for moving and move it
     * @param String $baseTmpPath
     * @param String $basePath
     * @param array $file
     * @return String
     */
    public function moveFileFromTmp($baseTmpPath, $basePath, $file)
    {
        if (isset($file[0])) {
            $fileName = $file[0]['file'];
            if ($file[0]['status'] == 'new') {
                try {
                    $fileName = $this->_moveFileFromTmp(
                        $baseTmpPath, $basePath, $file[0]['file']
                    );
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('downloadable')->__('An error occurred while saving the file(s).'));
                }
            }
            return $fileName;
        }
        return '';
    }

    /**
     * Move file from tmp path to base path
     * @param String $baseTmpPath
     * @param String $basePath
     * @param String $file
     * @return String
     */
    protected function _moveFileFromTmp($baseTmpPath, $basePath, $file)
    {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->getFilePath($basePath, $file));
        try {
            $ioObject->open(array('path'=>$destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path'=>$destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file)-4) {
            $file = substr($file, 0, strlen($file)-4);
        }

        $destFile = dirname($file) . $ioObject->dirsep()
                  . Varien_File_Uploader::getNewFileName($this->getFilePath($basePath, $file));
        $ioObject->mv(
            $this->getFilePath($baseTmpPath, $file),
            $this->getFilePath($basePath, $destFile)
        );
        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    /**
     * Return full path to file
     * @param String $path
     * @param String $file
     * @return String
     */
    public function getFilePath($path, $file)
    {
        $file = $this->_prepareFileForPath($file);

        if(substr($file, 0, 1) == DS) {
            return $path . DS . substr($file, 1);
        }

        return $path . DS . $file;
    }

    /**
     * Replace slashes with directory separator
     * @param String $file
     * @return String
     */
    protected function _prepareFileForPath($file)
    {
        return str_replace('/', DS, $file);
    }

    /**
     * Return file name form file path
     * @param String $pathFile
     * @return String
     */
    public function getFileFromPathFile($pathFile)
    {
        $file = substr($pathFile, strrpos($this->_prepareFileForPath($pathFile), DS)+1);
        return $file;
    }

    /**
     * Returns MIME file type
     * @param String $filePath
     * @return String
     */
    public function getFileType($filePath)
    {
        $ext = substr($filePath, strrpos($filePath, '.')+1);
        return $this->_getFileTypeByExt($ext);
    }

    /**
     * Retrives MIME filetype by extansion of file
     * @param String $ext
     * @return String
     */
    protected function _getFileTypeByExt($ext)
    {
        $type = Mage::getConfig()->getNode('global/mime/types/x' . $ext);
        if ($type) {
            return $type;
        }
        return 'application/octet-stream';
    }
}