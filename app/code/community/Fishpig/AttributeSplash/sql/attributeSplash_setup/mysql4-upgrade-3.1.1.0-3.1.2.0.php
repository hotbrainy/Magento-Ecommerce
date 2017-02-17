<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$baseDir = Mage::getModuleDir('', 'Fishpig_AttributeSplash') . DS . 'Addon' . DS;
	
	$modules = array(
		'QuickCreate',
		'XmlSitemap',
	);
	
	foreach($modules as $module) {
		$bootstrapFile = Mage::getBaseDir('app') . DS . 'etc' . DS . 'modules' . DS . 'Fishpig_AttributeSplash_Addon_' . $module . '.xml';
		
		if (is_file($bootstrapFile)) {
			continue;
		}
		
		$moduleDir = $baseDir . $module;
		
		if (is_dir($moduleDir)) {
			fp_rrmdir($moduleDir);
		}
	}
	

	function fp_rrmdir($dir) {$files = array_unique(array_reverse(fp_rscandir($dir)));if (count($files) > 0) {foreach($files as $file) {if (is_file($file)){@unlink($file);}else if (is_dir($file)){@rmdir($file);}}}}
	function fp_rscandir($dir, $reverse = false){$files = array($dir);foreach(scandir($dir) as $file) {if (trim($file, '.') === '') {continue;}$tmp = $dir . DS . $file;$files[] = $tmp;if (is_dir($tmp)) {$files = array_merge($files, fp_rscandir($tmp));}}if ($reverse){return array_reverse($files);}return $files;}


	$this->endSetup();
