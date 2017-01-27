<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    /**
     * Magento Root full path.
     *
     * @var null|string
     */
    protected $_baseDir = null;
    
    /**
     * Logging flag.
     * 
     * @var null|int
     */
    protected $_logging = null;
    
    /**
     * Path to utilities.
     *
     * @var null|string
     */
    protected $_utilPath = null;
    
    /**
     * Extension (for win binaries)
     *
     * @var null|string
     */
    protected $_utilExt  = null;
    
    /**
     * Index filename.
     *
     * @var string $_indexFilename
     */
    protected $_indexFilename = 'apptrian_imageoptimizer_index.data';
    
    /**
     * Index path.
     *
     * @var null|string $_indexPath
     */
    protected $_indexPath = null;
    
    /**
     * Index array.
     *
     * @var array $_index
     */
    protected $_index = array();
    
    /**
     * Total count of files in index.
     * 
     * @var integer $_indexTotalCount
     */
    protected $_indexTotalCount = 0;
    
    /**
     * Count of files that are optimized.
     * 
     * @var integer $_indexOptimizedCount
     */
    protected $_indexOptimizedCount = 0;
    
    /**
     * Returns extension version.
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        return (string) Mage::getConfig()->getNode()
            ->modules->Apptrian_ImageOptimizer->version;
    }
    
    /**
     * Based on provided configuration path returns configuration value.
     *
     * @param string $configPath
     * @return string
     */
    public function getConfig($configPath)
    {
        return Mage::getConfig()->getNode($configPath, 'default');
    }
    
    /**
     * Returns Magento Root full path.
     *
     * @return string
     */
    public function getBaseDir()
    {
        
        if ($this->_baseDir === null) {
            
            $this->_baseDir = Mage::getBaseDir();
            
        }
        
        return $this->_baseDir;
        
    }
    
    /**
     * Checks if exec() function is enabled in php and suhosin config.
     * 
     * @return boolean
     */
    public function isExecFunctionEnabled()
    {
        $r = false;
        
        // PHP disabled functions
        $phpDisabledFunctions = array_map(
            'strtolower', 
            array_map('trim', explode(',', ini_get('disable_functions')))
        );
        
        // Suhosin disabled functions
        $suhosinDisabledFunctions = array_map(
            'strtolower', 
            array_map(
                'trim', explode(',', ini_get('suhosin.executor.func.blacklist'))
            )
        );
        
        $disabledFunctions = array_merge(
            $phpDisabledFunctions, $suhosinDisabledFunctions
        );
        
        $disabled = false;
        
        if (in_array('exec', $disabledFunctions)) {
            $disabled = true;
        }
        
        if (function_exists('exec') === true && $disabled === false) {
            $r = true;
        }
        
        return $r;
    }
    
    /**
     * Optimized way of getting logging flag from config.
     * 
     * @return int
     */
    public function isLoggingEnabled()
    {
        if ($this->_logging === null) {
            
            $this->_logging = (int) $this->getConfig(
                'apptrian_imageoptimizer/utility/log_output'
            );
            
        }
        
        return $this->_logging;
    }
    
    /**
     * Based on config returns array of all paths that will be scaned for 
     * images.
     * 
     * @return array
     */
    public function getPaths()
    {
        
        $paths = array();
        
        $pathsString = trim(
            trim($this->getConfig('apptrian_imageoptimizer/general/paths'), ';')
        );
        
        $rawPaths = explode(';', $pathsString);
        
        foreach ($rawPaths as $p) {
            
            $trimmed = trim(trim($p), '/');
            
            $dirs = explode('/', $trimmed);
            
            $paths[] = implode(DS, $dirs);
            
        }
        
        return array_unique($paths);
        
    }
    
    /**
     * Optimizes single file.
     * 
     * @param string $filePath
     * @return boolean
     */
    public function optimizeFile($filePath)
    {
        
        $info = pathinfo($filePath);
        
        $output = array();
        
        switch (strtolower($info['extension'])) {
            case 'jpg':
            case 'jpeg':
                exec($this->getJpgUtil($filePath), $output, $returnVar);
                $type = 'jpg';
                break;
            case 'png':
                exec($this->getPngUtil($filePath), $output, $returnVar);
                $type = 'png';
                break;
            case 'gif':
                exec($this->getGifUtil($filePath), $output, $returnVar);
                $type = 'gif';
                break;
        }
        
        if ($returnVar == 126) {
            
            $error = $this->getConfig(
                'apptrian_imageoptimizer/utility/' . $type
            ) . ' is not executable.';
            
            Mage::log($error, null, 'apptrian_imageoptimizer.log');
            
            return false;
            
        } else {
            
            if ($this->isLoggingEnabled()) {
                
                Mage::log($filePath, null, 'apptrian_imageoptimizer.log');
                Mage::log($output, null, 'apptrian_imageoptimizer.log');
                
            }
            
            $permissions = (string) $this->getConfig(
                'apptrian_imageoptimizer/utility/permissions'
            );
            
            if ($permissions) {
                chmod($filePath, octdec($permissions));
            }
            
            return true;
            
        }
        
    }
    
    /**
     * Optimization process.
     * 
     * @return boolean
     */
    public function optimize()
    {
        
        $this->loadIndex();
        
        // Get Batch Size
        $batchSize = (int) $this->getConfig(
            'apptrian_imageoptimizer/general/batch_size'
        );
        
        // Get array of files for optimization limited by batch size
        $files = $this->getFiles($batchSize);
        
        $id          = '';
        $item        = array();
        $toUpdate    = array();
        $encodedPath = '';
        $decodedPath = '';
        $filePath    = '';
        
        // Optimize batch of files
        foreach ($files as $id => $item) {
            
            $encodedPath = $item['f'];
            $decodedPath = utf8_decode($encodedPath);
            $filePath    = realpath($decodedPath);
            
            // If image exists, optimize else remove it from database
            if (file_exists($filePath)) {
                
                if ($this->optimizeFile($filePath)) {
                    
                    $toUpdate[$id]['f'] = $encodedPath;
                    
                }
                
            } else {
                
                // Remove files that do not exist anymore from the index
                unset($this->_index[$id]);
                
            }
            
        }
        
        $i = '';
        $f = array();
        
        // Itereate over $toUpdate array and set modified time
        // (mtime) takes a split second to update
        foreach ($toUpdate as $i => $f) {
            
            $encodedPath = $f['f'];
            $decodedPath = utf8_decode($encodedPath);
            $filePath    = realpath($decodedPath);
            
            if (file_exists($filePath)) {
                
                // Update optimized file information in index
                $this->_index[$i]['t'] = filemtime($filePath);
                
            }
            
            // Free Memory
            unset($toUpdate[$i]);
            
        }
        
        return $this->saveIndex();
        
    }
    
    /**
     * Scan and reindex process.
     * 
     * @return boolean
     */
    public function scanAndReindex()
    {
        
        $this->loadIndex();
        
        $id          = '';
        $item        = array();
        $encodedPath = '';
        $decodedPath = '';
        $filePath    = '';
        
        // Check index for files that need to be updated and/or removed
        foreach ($this->_index as $id => $item) {
            
            $encodedPath = $item['f'];
            $decodedPath = utf8_decode($encodedPath);
            $filePath    = realpath($decodedPath);
            
            if (file_exists($filePath)) {
                if ($item['t'] != 0 && filemtime($filePath) != $item['t']) {
                    
                    // Update time to 0 in index so it can be optimized again
                    $this->_index[$id]['t'] = 0;
                    
                }
            } else {
                
                // Remove files that do not exist anymore from the index
                unset($this->_index[$id]);
                
            }
            
        }
        
        $paths    = $this->getPaths();
        $path     = '';
        
        // Scan for new files and add them to the index
        foreach ($paths as $path) {
            
            $this->scanAndReindexPath($path);
            
        }
        
        return $this->saveIndex();
        
    }
    
    /**
     * Scans provided path for images and adds them to index.
     * 
     * @param string $path
     */
    public function scanAndReindexPath($path)
    {
        
        $id          = '';
        $encodedPath = '';
        $filePath    = '';
        $file        = null;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->getBaseDir() . DS . $path,
                RecursiveDirectoryIterator::FOLLOW_SYMLINKS
            )
        );
        
        foreach ($iterator as $file) {
        
            if ($file->isFile()
                && preg_match(
                    '/^.+\.(jpe?g|gif|png)$/i', $file->getFilename()
                )
            ) {
        
                $filePath = $file->getRealPath();
                if (!is_writable($filePath)) {
                    continue;
                }
        
                $encodedPath = utf8_encode($filePath);
                $id          = md5($encodedPath);

                // Add only if file is not already in the index
                if (!isset($this->_index[$id])) {
                    $this->_index[$id] = array('f' => $encodedPath, 't' => 0);
                }
        
            }
        
            // Free Memory
            $file = null;
        
        }
        
        // Free Memory
        $iterator = null;
        
    }
    
    /**
     * Checks if server OS is Windows
     *
     * @return bool
     */
    public function isWindows()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Alias for getUtil() and .gif
     *
     * @param string $filePath
     * @return string
     */
    public function getGifUtil($filePath)
    {
        return $this->getUtil('gif', $filePath);
    }
    
    /**
     * Alias for getUtil() and .jpg
     *
     * @param string $filePath
     * @return string
     */
    public function getJpgUtil($filePath)
    {
        return $this->getUtil('jpg', $filePath);
    }
    
    /**
     * Alias for getUtil() and .png
     *
     * @param string $filePath
     * @return string
     */
    public function getPngUtil($filePath)
    {
        return $this->getUtil('png', $filePath);
    }
    
    /**
     * Formats and returns the shell command string for an image optimization
     * utility
     *
     * @param string $type - This is image type. Valid values gif|jpg|png
     * @param string $filePath - Path to the image to be optimized
     * @return string
     */
    public function getUtil($type, $filePath)
    {
        
        $exactPath = $this->getConfig(
            'apptrian_imageoptimizer/utility/' . $type . '_path'
        );
        
        // If utility exact path is set use it
        if ($exactPath != '') {
            
            $cmd = $exactPath;
            
        // Use path to extension's local utilities
        } else {
            
            $cmd = $this->getUtilPath() . DS . $this->getConfig(
                'apptrian_imageoptimizer/utility/' . $type
            ) . $this->getUtilExt();
            
        }
        
        $cmd .= ' ' . $this->getConfig(
            'apptrian_imageoptimizer/utility/' . $type . '_options'
        );
        
        return str_replace('%filepath%', $filePath, $cmd);
        
    }
    
    /**
     * Gets and stores utility extension.
     * Checks server OS and determine utility extension.
     *
     * @return string
     */
    public function getUtilExt()
    {
        if ($this->_utilExt === null) {
            
            $this->_utilExt = $this->isWindows() ? '.exe' : '';
            
        }
        
        return $this->_utilExt;
    }
    
    /**
     * Gets and stores path to utilities.
     * Checks server OS and config to determine the path where
     * image optimization utilities are.
     *
     * @return string
     */
    public function getUtilPath()
    {
        if ($this->_utilPath === null) {
            
            $useSixtyFourBit = (int) $this->getConfig(
                'apptrian_imageoptimizer/utility/use64bit'
            );
            
            if ($useSixtyFourBit) {
                $bit = '64';
            } else {
                $bit = '32';
            }
            
            $os = $this->isWindows() ? 'win' . $bit : 'elf' . $bit;
            
            $pathString = trim(
                trim(
                    $this->getConfig('apptrian_imageoptimizer/utility/path')
                ), 
                '/'
            );
            
            $dirs = explode('/', $pathString);
            $path = implode(DS, $dirs);
            
            $this->_utilPath = $this->getBaseDir() . DS . $path . DS . $os;
            
        }
        
        return $this->_utilPath;
    }
    
    /**
     * Returns index path.
     * 
     * @return string
     */
    public function getIndexPath()
    {
        if ($this->_indexPath === null) {
            
            $this->_indexPath = Mage::getBaseDir('var') . DS 
                . $this->_indexFilename;
            
        }
        
        return $this->_indexPath;
    }
    
    /**
     * Returns array of files for optimization limited by $batchSize.
     * 
     * @param int $batchSize
     */
    public function getFiles($batchSize)
    {
        
        $files   = array();
        $counter = 0;
        
        foreach ($this->_index as $id => $f) {
            
            if ($counter == $batchSize) {
                break;
            }
            
            if ($f['t'] == 0) {
                $files[$id] = $f;
                $counter++;
            }
            
        }
        
        return $files;
        
    }
    
    /**
     * Returns count of indexed and optmized files.
     *
     * @return array
     */
    public function getFileCount()
    {
        
        $this->loadIndex();
        
        $r['indexed']   = $this->_indexTotalCount;
        $r['optimized'] = $this->_indexOptimizedCount;
        
        // Free memory
        $this->_index = null;
        
        return $r;
        
    }
    
    /**
     * Clear index (Empty index file).
     *
     * @return boolean
     */
    public function clearIndex()
    {
        
        $r = file_put_contents($this->getIndexPath(), '', LOCK_EX);
        
        if ($r === false) {
            Mage::log('Clear index operation failed.');
        } else {
            $r = true;
        }
        
        return $r;
        
    }
    
    /**
     * Load index from a file.
     * 
     */
    public function loadIndex()
    {
        
        $filePath = $this->getIndexPath();
        
        if (file_exists($filePath)) {
            
            $line = '';
            $l    = array();
            $id   = '';
            $file = array();
            
            $str = file_get_contents($filePath);
            
            if ($str != '') {
                
                $data = explode("\n", $str);
                
                // Free Memory
                unset($str);
                
                $this->_indexTotalCount = count($data);
                
                $i = 0;
                
                for ($i = 0; $i < $this->_indexTotalCount; $i++) {
                    
                    $line      = $data[$i];
                    $l         = explode('|', $line);
                    $id        = (string) $l[0];
                    $file['f'] = (string) $l[1];
                    $file['t'] = (int) $l[2];
                    
                    $this->_index[$id] = $file;
                    
                    if ($file['t'] > 0) {
                        $this->_indexOptimizedCount++;
                    }
                    
                    // Free Memory
                    unset($data[$i]);
                    
                }
                
                // Free Memory
                $data = null;
            
            }
                
            if (!$this->_index) {
                $this->_index = array();
            }
            
        }
        
    }
    
    /**
     * Save index to a file.
     * 
     * @return boolean
     */
    public function saveIndex()
    {
        
        $id   = '';
        $f    = '';
        $data = array();
        $c    = 0;
        $b    = 0;
        $r    = false;
        
        // Truncate existing index file
        $this->clearIndex();
        
        foreach ($this->_index as $id => $f) {
            
            // str_replace() removes | from filename because | is delimiter
            $data[] = sprintf(
                '%s|%s|%d', $id, str_replace('|', '', $f['f']), $f['t']
            );
            
            // Free memory
            unset($this->_index[$id]);
            
            if ($c == 100000) {
                
                // Save part of the file
                $r = $this->saveToFile($data, $b);
                
                // Free memory
                $data = array();
                
                // Increment batch
                $b++;
                
                // Reset count
                $c = 0;
                
            } else {
                
                // Increment count
                $c++;
                
            }
        
        }
        
        // Save last part of the file
        $r = $this->saveToFile($data, $b);
        
        // Free memory
        $this->_index = null;
        
        if ($r === false) {
            Mage::log('Writting index to a file failed.');
        } else {
            $r = true;
        }
        
        return $r;
        
    }
    
    /**
     * Saves batch of data to a file.
     * 
     * @param array $data
     * @param int $b
     */
    public function saveToFile($data, $b)
    {
        
        $r = true;
        
        if (count($data) > 0) {
        
            $fh = fopen($this->getIndexPath(), 'a');
            
            if ($b != 0) {
                fwrite($fh, "\n");
            }
                
            $r = fwrite($fh, implode("\n", $data));
            
            fclose($fh);
            
        }

        return $r;
        
    }
    
}
