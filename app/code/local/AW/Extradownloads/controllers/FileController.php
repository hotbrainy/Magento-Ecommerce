<?php

/**
 * Controller for file response
 */
class AW_Extradownloads_FileController extends Mage_Core_Controller_Front_Action
{
    /**
     * Declare headers and content file in responce for file download
     *
     * @param string $fileName
     * @param string $content set to null to avoid starting output, $contentLength should be set explicitly in that case
     * @param string $contentType
     * @param int $contentLength explicit content length, if strlen($content) isn't applicable
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null)
    {
        $file_id = $this->getRequest()->getParam('id', false);
        if (!$file_id){
            # Redirect noroute
            $this->_forward('noroute');
            return;
        }
        $file = Mage::getModel('extradownloads/file')
            ->setStoreId(Mage::app()->getStore()->getId())     
            ->load($file_id);

        if ((!$file->getId()) || (!$file->getVisible())){
            # Redirect noroute
            $this->_forward('noroute');
            return;            
        }

        $isEnabled = Mage::getStoreConfig('extradownloads/general/enabled');
        $product = Mage::getModel('catalog/product')->load($file->getProductId());
        if (!$isEnabled || is_null($product->getId()) || !$product->getExtradownloadsEnabled()) {
            # Redirect noroute
            $this->_forward('noroute');
            return;
        }

        $counter = $file->getDownloads() ? $file->getDownloads() : 0;
        $file->setExtradownloadsId( $file->getId() );
        $file->setDownloads(++ $counter);       
        

        if ($file->getType() == AW_Extradownloads_Helper_File::EXTRA_TYPE_URL){
            # Forward Link
            $this->_redirectUrl($file->getUrl());
            $file->save();
        } elseif($file->getType() == AW_Extradownloads_Helper_File::EXTRA_TYPE_FILE) {
            # Return file
            if ($file->getFile() && file_exists($file->getFilePath())){
                $contentType = $file->getFileType();
                $contentLength = $file->getFileContentLength();
                $content = $file->getFileContent();
                $fileName = $file->getFileName();

                $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->setHeader('Pragma', 'public', true)
                    ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                    ->setHeader('Content-type', $contentType, true)
                    ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
                    ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
                    ->setHeader('Last-Modified', date('r'));
                if (!is_null($content)) {
                    $this->getResponse()->setBody($content);
                }
                $file->save();
            } else {
                $this->_forward('noroute');
            }
        } 
        return $this;
    }

    /**
     * Returns file to recepient
     */
    public function getAction()
    {
        $fileName   = 'filename.file';
//        $content    = $this->getFile();
        $content    = null;
        $this->_prepareDownloadResponse($fileName, $content);
    }
}
