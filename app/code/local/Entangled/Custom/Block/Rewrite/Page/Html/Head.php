<?php

class Entangled_Custom_Block_Rewrite_Page_Html_Head extends Mage_Page_Block_Html_Head {

    /**
     * Get HEAD HTML with CSS/JS/RSS definitions
     * (actually it also renders other elements, TODO: fix it up or rename this method)
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        // separate items by types
        $lines  = array();
        foreach ($this->_data['items'] as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                continue;
            }
            $if     = !empty($item['if']) ? $item['if'] : '';
            $params = !empty($item['params']) ? $item['params'] : '';
            switch ($item['type']) {
                case 'js':        // js/*.js
                case 'skin_js':   // skin/*/*.js
                case 'js_css':    // js/*.css
                case 'skin_css':  // skin/*/*.css
                    $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                    break;
                default:
                    $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                    break;
            }
        }

        // prepare HTML
        $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
        $html   = '';
        foreach ($lines as $if => $items) {
            if (empty($items)) {
                continue;
            }
            if (!empty($if)) {
                // open !IE conditional using raw value
                if (strpos($if, "><!-->") !== false) {
                    $html .= $if . "\n";
                } else {
                    $html .= '<!--[if '.$if.']>' . "\n";
                }
            }

            // static and skin css
            $html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />'."\n",
                empty($items['js_css']) ? array() : $items['js_css'],
                empty($items['skin_css']) ? array() : $items['skin_css'],
                $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
            );

            // static and skin javascripts
            $html .= $this->_prepareStaticAndSkinElements('<script %s type="text/javascript" src="%s"></script>' . "\n",
                empty($items['js']) ? array() : $items['js'],
                empty($items['skin_js']) ? array() : $items['skin_js'],
                $shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
            );

            // other stuff
            if (!empty($items['other'])) {
                $html .= $this->_prepareOtherHtmlHeadElements($items['other']) . "\n";
            }

            if (!empty($if)) {
                // close !IE conditional comments correctly
                if (strpos($if, "><!-->") !== false) {
                    $html .= '<!--<![endif]-->' . "\n";
                } else {
                    $html .= '<![endif]-->' . "\n";
                }
            }
        }
        return $html;
    }

    /**
     * Merge static and skin files of the same format into 1 set of HEAD directives or even into 1 directive
     *
     * Will attempt to merge into 1 directive, if merging callback is provided. In this case it will generate
     * filenames, rather than render urls.
     * The merger callback is responsible for checking whether files exist, merging them and giving result URL
     *
     * @param string $format - HTML element format for sprintf('<element src="%s"%s />', $src, $params)
     * @param array $staticItems - array of relative names of static items to be grabbed from js/ folder
     * @param array $skinItems - array of relative names of skin items to be found in skins according to design config
     * @param callback $mergeCallback
     * @return string
     */
    protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems,
                                                      $mergeCallback = null)
    {
        $designPackage = Mage::getDesign();
        $baseJsUrl = Mage::getBaseUrl('js');
        $items = array();
        if ($mergeCallback && !is_callable($mergeCallback)) {
            $mergeCallback = null;
        }

        // get static files from the js folder, no need in lookups
        foreach ($staticItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? Mage::getBaseDir() . DS . 'js' . DS . $name : $baseJsUrl . $name;
            }
        }

        // lookup each file basing on current theme configuration
        foreach ($skinItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $designPackage->getFilename($name, array('_type' => 'skin'))
                    : $designPackage->getSkinUrl($name, array());
            }
        }

        $html = '';
        foreach ($items as $params => $rows) {
            // attempt to merge
            $mergedUrl = false;
            if ($mergeCallback) {
                $mergedUrl = call_user_func($mergeCallback, $rows);
            }
            // render elements
            $params = trim($params);
            $params = $params ? ' ' . $params : '';
            if ($mergedUrl) {
                $html .= sprintf($format, $mergedUrl, $params);
            } else {
                foreach ($rows as $src) {
                    if (strpos($format, 'text/javascript')) {
                        $html .= sprintf($format, $params, $src);
                    } else {
                        $html .= sprintf($format, $src, $params);
                    }
                }
            }
        }
        return $html;
    }

}