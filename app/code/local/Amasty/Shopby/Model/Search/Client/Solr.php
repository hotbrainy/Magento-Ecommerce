<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

class Amasty_Shopby_Model_Search_Client_Solr extends Enterprise_Search_Model_Client_Solr
{
    public function search($query, $offset = 0, $limit = 10, $params = array(), $method = self::METHOD_GET)
    {
        if (is_array($params) && isset($params['fq'])) {
            $params['fq'] = $this->splitFq($params['fq']);
        }

        return parent::search($query, $offset, $limit, $params, $method);
    }

    protected function splitFq($fqString)
    {
        $parts = explode(' AND ', $fqString);
        $catsTagged = false;
        foreach ($parts as &$part) {
            // keep only one tag at the beginning
            if (preg_match('/{!tag=([^}]+)}/', $part, $match)) {
                $part = $match[0] . str_replace($match[0], '', $part);
                if (strpos($part, '{!tag=catt}') === 0) {
                    $catsTagged = true;
                }
            }
        }
        if ($catsTagged) {
            $parts = array_filter($parts, array($this, 'filterExcludeUntaggedCat'));
        } else {
            array_walk($parts, array($this, 'walkTagCategory'));
        }
        return $parts;
    }

    protected function filterExcludeUntaggedCat($part)
    {
        return strpos($part, 'category_ids:') !== 0;
    }

    protected function walkTagCategory(&$part)
    {
        $part = preg_replace('@^(category_ids:\d+)$@', '{!tag=catt}$1', $part);
    }
}
