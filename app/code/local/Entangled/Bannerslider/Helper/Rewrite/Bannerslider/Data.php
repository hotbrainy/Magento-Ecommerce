<?php

class Entangled_Bannerslider_Helper_Rewrite_Bannerslider_Data extends Magestore_Bannerslider_Helper_Data {

    public static function uploadMobileBannerImage() {
        $banner_image_path = Mage::getBaseDir('media') . DS . 'bannerslider';
        $image = "";
        if (isset($_FILES['mobile_image']['name']) && $_FILES['mobile_image']['name'] != '') {
            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader('mobile_image');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(true);

                $uploader->setFilesDispersion(true);

                $uploader->save($banner_image_path, $uploader->getCorrectFileName($_FILES['mobile_image']['name']));
                // Add by Hoang Vuong: 30/08/2013
                $image = substr(strrchr($uploader->getUploadedFileName(), "/"), 1);
            } catch (Exception $e) {

            }

            // $image = $_FILES['image']['name'];
        }
        return $image;
    }

    public function getBannerImage($image) {
        $name = $this->reImageName($image);
        return $image ? Mage::getBaseUrl('media') . 'bannerslider' . '/' . $name : "";
    }

}