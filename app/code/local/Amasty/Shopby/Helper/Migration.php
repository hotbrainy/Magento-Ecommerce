<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Helper_Migration extends Mage_Core_Helper_Abstract
{
    protected $info;

    public function getMigrationsInfo()
    {
        if (is_null($this->info)) {
            $this->calculateInfo();
        }
        return $this->info;
    }

    /**
     * @return string|null
     */
    public function getRealStateVersion()
    {
        if (is_null($this->info)) {
            $this->calculateInfo();
        }

        $lastSuccessVersion = '0.0.0';

        foreach ($this->info as $state) {
            if ($state['success']) {
                $lastSuccessVersion = $state['version'];
            }
        }

        return $lastSuccessVersion;
    }

    /**
     * @return string|null
     */
    public function getRealNextStateVersion()
    {
        if (is_null($this->info)) {
            $this->calculateInfo();
        }

        $realVersion = $this->getRealStateVersion();
        $nextVersion = null;
        foreach ($this->info as $state) {
            if (version_compare($state['version'], $realVersion) > 0) {
                $nextVersion = $state['version'];
                break;
            }
        }

        return $nextVersion;
    }

    protected function calculateInfo()
    {
        $this->info = array();

        $tests = array();
        $searchDir = 'app/code/local/Amasty/Shopby/sql/amshopby_setup/';
        $names = scandir($searchDir);
        foreach ($names as $name) {
            if (preg_match('@(\d)\.(\d)\.(\d)\.php$@', $name, $matches)) {
                $version = $matches[1] . '.' . $matches[2] . '.' . $matches[3];
                $content = file_get_contents($searchDir . $name);
                $newTests = $this->parseTests($content);
                $tests = array_merge($tests, $newTests);
                $success = array_search(false, $tests) === false;

                $this->info[] = array(
                    'version' => $version,
                    'newTests' => $newTests,
                    'success' => $success,
                );
            }
        }
    }

    protected function parseTests($content)
    {
        preg_match_all('/@Migration\s+(\w+:[^:]*):(.*)/', $content, $matches, PREG_SET_ORDER);
        $tests = array();
        foreach ($matches as $match) {
            $key = trim($match[1]);
            $waitingResult = trim($match[2]);
            $actualResult = $this->performTest($key);
            $tests[$key] = $actualResult == $waitingResult;
        }
        return $tests;
    }

    protected function performTest($key)
    {
        list($type, $args) = explode(':', $key);
        $args = explode('|', $args);

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');

        switch ($type) {
            case 'table_exist':
                try {
                    $tableName = $resource->getTableName($args[0]);
                } catch (Exception $e) {
                    return false;
                }
                $result = $connection->isTableExists($tableName);
                return $result;
                break;
            case 'field_exist':
                try {
                    $tableName = $resource->getTableName($args[0]);
                } catch (Exception $e) {
                    return false;
                }
                $field = trim($args[1]);
                $result = $connection->tableColumnExists($tableName, $field);
                return $result;
                break;
            default:
                throw new Exception('Undefined test type: ' . $type);
        }
    }

}
