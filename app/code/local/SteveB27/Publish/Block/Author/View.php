<?php
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */

class SteveB27_Publish_Block_Author_View extends Mage_Core_Block_Template
{
    public function getCurrentAuthor()
    {
        return Mage::registry('current_author');
    }
    
    public function getSocialHtml($author = null)
    {
		if(!$author) {
			$author = $this->getCurrentAuthor();
		} elseif (is_numeric($author)) {
			$author = Mage::getModel('publish/author')->load($author);
		}
		
		$has_social = false;
		
		$social = array(
			'twitter'		=> 'Twitter',
			'facebook'		=> 'Facebook',
			'googleplus'	=> 'Google+',
			'youtube'		=> 'YouTube',
			'vimeo'			=> 'Vimeo',
			'wordpress'		=> 'WordPress',
			'pinterest'		=> 'Pinterest',
			'linkedin'		=> 'LinkedIn',
			'blogger'		=> 'Blogger',
			'amazon'		=> 'Amazon',
		);
		
		$html = '<ul class="socila_info">';
		foreach($social as $key => $title) {
			$field = 'social_'.$key;
			$url = $author->getData($field);
			if($url == '') continue;
			$has_social = true;
			$icon_class = $key;
			$html .= sprintf("<li><a target='_blank' href='%s' title='%s'><i class='fa fa-%s'></i></a></li>",$url,$title,$icon_class);
		}
		$html .= '</ul>';
		
		if($has_social) {
			return $html;
		} else {
			return '';
		}
	}
}