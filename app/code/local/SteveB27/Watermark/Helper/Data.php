<?php
/**
 * @Author: Alex Pelletier
 * @Date:   2016-04-21 22:46:59
 * @Last Modified by:   Steven Brown
 * @Last Modified time: 2016-05-06 15:35:27
 */
?>
<?php
class SteveB27_Watermark_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getBook(Mage_Downloadable_Model_Link_Purchased_Item $item){
		$include = array(
			Mage::getBaseDir() . DS . "lib" . DS . "Watermark" . DS . "fpdf",
			Mage::getBaseDir('lib') . DS . "Watermark" . DS . "fpdi",
			Mage::getBaseDir('lib') . DS . "Watermark" . DS . "pdfwatermarker",
		);
		set_include_path(get_include_path() . PS . implode(PS,$include));
		
		// get Magento models
		$linkPurchased = Mage::getModel('downloadable/link_purchased')->load($item->getPurchasedId());
		$customer = Mage::getModel('customer/customer')->load($linkPurchased->getCustomerId());
		
		$string = Mage::getStoreConfig('watermark/watermark/string');
		$string = str_ireplace('{{email}}',$customer->getEmail(),$string);
		$string = str_ireplace('{{firstname}}',$customer->getFirstname(),$string);
		$string = str_ireplace('{{lastname}}',$customer->getLastname(),$string);
		
		$cleanhash = preg_replace("/[^A-Za-z0-9]/", "", $item->getLinkHash());

		$watermarkedPDF = $this->getCachePath() . $cleanhash . '-' . md5($string) . ".pdf";
		
		if(!file_exists($watermarkedPDF)) {
			//create watermark 
			$watermarkImage = $cleanhash . '-' . md5($string) . ".png";
			$imageStoragePath = $this->getCachePath() . $watermarkImage;
			$originalPdf = Mage::getModel('downloadable/link')->getBasePath() . DS . str_replace('/',DS,$item->getLinkFile());
		
			$im = imagecreatetruecolor(400, 15);
			imagefilledrectangle($im, 0, 0, 399, 29, imagecolorallocate($im, 255, 255, 255));
			$font = Mage::getBaseDir('lib') . DS . 'Watermark' . DS . 'arialbd.ttf';
			$black = imagecolorallocate($im, 0, 0, 0);
			
			imagettftext($im, 10, 0, 10, 10, $black, $font,$string);
			imagepng($im, $imageStoragePath);

			//Specify path to image
			$watermark = new PDFWatermark($imageStoragePath); 
			//Specify the path to the existing pdf, the path to the new pdf file, and the watermark object
			$watermarker = new PDFWatermarker($originalPdf,$watermarkedPDF,$watermark); 
			//Set the position
			$watermarker->setWatermarkPosition('topright');
			//Save the new PDF to its specified location
			$watermarker->watermarkPdf();
			//Remove the watermark png
			unlink($imageStoragePath);
		}

		header("Content-type:application/pdf");

		header("Content-Disposition:attachment;filename=\"" . basename($item->getLinkFile()) . "\"");
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		readfile($watermarkedPDF);
	}
	
	/**
     * Retrieve cache files path
     *
     * @return string
     */
    public static function getCachePath()
    {
        return Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'cache' . DS . 'links' . DS;
    }
}