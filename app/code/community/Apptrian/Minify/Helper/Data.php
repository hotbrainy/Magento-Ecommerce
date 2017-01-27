<?php
/**
 * @category  Apptrian
 * @package   Apptrian_Minify
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_Minify_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Array of paths that will be scaned for css and js files.
     * 
     * @var array
     */
    protected $_paths = null;
    
    /**
     * Returns extension version.
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        return (string) Mage::getConfig()
            ->getNode()->modules->Apptrian_Minify->version;
    }
    
    /**
     * Returns array of paths that will be scaned for css and js files.
     * 
     * @return array
     */
    public function getPaths()
    {
        if ($this->_paths === null) {
            
            $list         = array();
            $baseDirMedia = Mage::getBaseDir('media');
            $css          = $baseDirMedia . DS . 'css';
            $cssSecure    = $baseDirMedia . DS . 'css_secure';
            $js           = $baseDirMedia . DS . 'js';
            
            if (file_exists($css)) {
                $list[] = $css;
            }
            
            if (file_exists($cssSecure)) {
                $list[] = $cssSecure;
            }
            
            if (file_exists($js)) {
                $list[] = $js;
            }
            
            $this->_paths = $list;
            
        }
        
        return $this->_paths;
    }
    
    /**
     * Minifies CSS and JS files.
     * 
     */
    public function process()
    {
        // Get remove important comments option
        $removeComments = (int) Mage::getConfig()->getNode(
            'apptrian_minify/minify_css_js/remove_comments', 'default'
        );
        
        foreach ($this->getPaths() as $path) {
            
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $path, RecursiveDirectoryIterator::FOLLOW_SYMLINKS
                )
            );
            
            foreach ($iterator as $filename => $file) {
                
                if ($file->isFile() 
                    && preg_match('/^.+\.(css|js)$/i', $file->getFilename())
                ) {
                    
                    $filePath = $file->getRealPath();
                    if (!is_writable($filePath)) {
                        Mage::log(
                            'Minification failed for ' 
                            . $filePath . ' File is not writable.'
                        );
                        continue;
                    }
                    
                    //This is available from php v5.3.6 
                    //$ext = $file->getExtension();
                    // Using this for compatibility
                    $ext         = strtolower(
                        pathinfo($filePath, PATHINFO_EXTENSION)
                    );
                    $optimized   = '';
                    $unoptimized = file_get_contents($filePath);
                    
                    // If it is 0 byte file or cannot be read
                    if (!$unoptimized) {
                        Mage::log('File ' . $filePath . ' cannot be read.');
                        continue;
                    }
                    
                    // CSS files
                    if ($ext == 'css') {
                        
                        if ($removeComments == 1) {
                            
                            $optimized = Minify_CSS::minify(
                                $unoptimized, array('preserveComments' => false)
                            );
                            
                        } else {
                            
                            $optimized = Minify_CSS::minify($unoptimized);
                            
                        }
                        
                    // JS files
                    } else {
                        
                        
                        if ($removeComments == 1) {
                            
                            $optimized = JSMinMax::minify($unoptimized);
                            
                        } else {
                            
                            $optimized = JSMin::minify($unoptimized);
                            
                        }
                        
                        
                    }
                    
                    
                    // If optimization failed
                    if (!$optimized) {
                        Mage::log('File ' . $filePath . ' was not minified.');
                        continue;
                    }
                    
                    
                    if (file_put_contents(
                        $filePath, $optimized, LOCK_EX
                    ) === false) {
                        
                        Mage::log('Minification failed for ' . $filePath);
                        
                    }
                    
                }
                
            }
            
        }
        
    }
    
}
