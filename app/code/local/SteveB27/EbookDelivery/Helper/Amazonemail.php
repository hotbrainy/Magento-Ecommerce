<?php

class SteveB27_EbookDelivery_Helper_Amazonemail extends Mage_Core_Helper_Abstract
{
	public $allowed = array(
		'Kindle Mobi Format'=> 'MOBI',
		'Kindle Format'		=> 'AZW',
		'Microsoft Doc'		=> 'DOC',
		'Microsoft Word'	=> 'DOCX',
		'HTML'				=> 'HTML',
		'HTM'				=> 'HTM',
		'RTF'				=> 'RTF',
		'Text'				=> 'TXT',
		'JPEG'				=> 'JPEG',
		'JPG'				=> 'JPG',
		'GIF'				=> 'GIF',
		'PNG'				=> 'PNG',
		'BMP'				=> 'BMP',
		'PDF'				=> 'PDF'
	);
	public function getFormInput()
	{
		$input = <<< EOI
		<ul class="form-list">
			<li class="control">
				<label for="delivery_amazonemail_device_nickname">Device Nickname</label>
				<input type="text" name="delivery[{{index}}][amazonemail][device_nickname]" />
			</li>
			<li class="control">
				<label for="delivery_amazonemail_device_email">Delivery Email</label>
				<input type="text" name="delivery[{{index}}][amazonemail][device_email]" class="validate-amazonemail" />
			</li>
		</ul>
EOI;
		
		return $input;
	}
	
	public function deliver($email,$links)
	{
        $hasMobi = false;
        foreach($links as $linkitem) {
            if(explode(".", $linkitem->getLinkFile())[1] == "mobi"){
                $hasMobi = true;
            }
        }
		foreach($links as $linkitem) {
            $fileType = explode(".", $linkitem->getLinkFile())[1];
            if($fileType == "mobi" || ($fileType == "pdf" && !$hasMobi)){
                $link = Mage::getModel('downloadable/link_purchased_item')->load($linkitem->getId());
                $product = Mage::getModel("catalog/product")->load($link->getProductId());
                // Temporarily make link shareable for delivery
                $shareable = $link->getIsShareable();
                $link->setIsShareable(Mage_Downloadable_Model_Link::LINK_SHAREABLE_YES);
                $link->save();

                $linkUrl = Mage::getUrl('downloadable/download/link', array('id' => $link->getLinkHash(), '_secure' => true));
                // Send link

                $this->sendAmazonemail($email, $linkUrl, $link->getLinkFile(),$product->getName());

                // Reset link shareability
                $link->setIsShareable($shareable);
                $link->save();
            }
		}
	}
	
	public function sendAmazonemail($email, $link, $filename,$productName = false)
	{
		$filename = basename($filename);
		$extension = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));
		
		if(!in_array($extension, $this->allowed)) {
			return false;
		}
		
		$mail = new Zend_Mail();

		$mailBody    = "You are receiving this email because this email address is listed as an Amazon Kindle Device at ";
		$mailBody	.= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$mailBody	.= ". To unsubscribe, log in and manage your devices.";
		
		$mail->setBodyText($mailBody)
			->addTo($email, 'Amazon Kindle Device')
			->setFrom(Mage::getStoreConfig('trans_email/ident_custom1/email'),
				Mage::getStoreConfig('trans_email/ident_custom1/name'))
			->setSubject('Entangled Ebook Delivery Service');

        $fileType = explode(".", $filename)[1];
        if($productName && $fileType == "pdf"){
            $filename = $productName.".".$fileType;
            $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
            $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
        }

		//file content is attached
		try {
			$attachment = new Zend_Mime_Part(file_get_contents($link));
			$attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
			$attachment->encoding = Zend_Mime::ENCODING_BASE64;
			$attachment->filename = $filename;
			$attachment->type = mime_content_type($link);
		        
			$mail->addAttachment($attachment);
			if(!$mail->send()){
                Mage::log("Problem sending book ".$filename,null,"delivery.log",true);
            }
		} catch (Exception $e) {
		    Mage::log("Problem sending book ".$filename." - ".$e->getMessage(),null,"delivery.log",true);
			Mage::logException($e);
		}
	}
}