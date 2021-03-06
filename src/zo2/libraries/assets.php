<?php

/**
 * Zo2 (http://www.zo2framework.org)
 * A powerful Joomla template framework
 *
 * @link        http://www.zo2framework.org
 * @link        http://github.com/aploss/zo2
 * @author      ZooTemplate <http://zootemplate.com>
 * @copyright   Copyright (c) 2013 APL Solutions (http://apl.vn)
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class exists checking
 */
if (!class_exists('Zo2Assets')) {

    /**
     * @uses
     * This class use for managed ALL asset stuffs
     * @rule
     * All asset stuffs must be save under <core>|<template>/assets directory
     */
    class Zo2Assets {

        /**
         * Singleton instance
         * @var Zo2Assets
         */
        public static $instance;

        /**
         * Array of added css files
         * @var array
         */
        private $_stylesheets = array();

        /**
         * Array of added custom stylesheet scripts
         * @var array
         */
        private $_stylesheetDeclarations = array();

        /**
         * Array of added js files
         * @var array
         */
        private $_javascripts = array();

        /**
         * Array of added custom javascripts
         * @var array
         */
        private $_javascriptDeclarations = array();

        /**
         *
         * @var type
         */
        private $_assets = array();

        /**
         * Construct method
         * We do not allow create new instance directly. Must go via getInstance
         */
        protected function __construct() {
            $assetsFile = $this->getAssetFile('assets.json', 'path');
            if ($assetsFile) {
                $this->_assets = json_decode(file_get_contents($assetsFile));
            }
            $application = JFactory::getApplication();
            /* Dynamic load by backend options */
            if ($application->isAdmin()) {
                if (Zo2Framework::isJoomla25()) {
                    /* For Joomla! 2.5 we need add jQuery into head */
                    $document = JFactory::getDocument();
                    $document->addScript(Juri::root() . '/plugins/system/zo2/assets/vendor/jquery/jquery-1.9.1.min.js');
                    $document->addScript(Juri::root() . '/plugins/system/zo2/assets/vendor/jquery/jquery.noConflict.js');
                }
            } else {
                /* Allow user turn of jQuery if needed */
                if (Zo2Framework::isJoomla25() && Zo2Framework::get('enable_jquery', 1) == 1) {
                    /* For Joomla! 2.5 we need add jQuery into head */
                    $document = JFactory::getDocument();
                    $document->addScript(Juri::root() . '/plugins/system/zo2/assets/vendor/jquery/jquery-1.9.1.min.js');
                    $document->addScript(Juri::root() . '/plugins/system/zo2/assets/vendor/jquery/jquery.noConflict.js');
                }
                /* Responsive */
                if (Zo2Framework::get('responsive_layout'))
                    $this->addStyleSheet('css/non-responsive.css');
            }
        }

        /**
         *
         * @return Zo2Assets
         */
        public static function getInstance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
            }
            if (isset(self::$instance)) {
                return self::$instance;
            }
        }

        /**
         *
         * @param type $version
         * @return boolean
         */
        private function _isMatchJVersion($version) {
            $jVer = new JVersion();
            $jVer = explode('.', $jVer->RELEASE);
            $version = explode('.', $version);
            /* Match major version */
            if ($version[0] == '*')
                return true;
            if ($jVer[0] == $version[0]) {
                if ($version[1] == '*')
                    return true;
                else {
                    return $jVer[1] >= $version[1];
                }
            }
            return false;
        }

        /**
         * Get asset file with relative path
         * @param type $file
         * @return string
         */
        public function getAssetFile($file, $return = null) {
            /* Template override */
            $paths[] = Zo2HelperPath::getTemplateFilePath('assets/' . $file, null);
            /* Core assets */
            $paths[] = Zo2HelperPath::getZo2FilePath('assets/' . $file, null);
            /* Find in array which the first exists file */
            foreach ($paths as $path) {
                $physicalPath = Zo2HelperPath::getPath($path);
                if (JFile::exists($physicalPath)) {
                    if (is_null($return)) {
                        return $path;
                    } elseif ($return == 'url') {
                        return Zo2HelperPath::getUrl($path);
                    } elseif ($return == 'path') {
                        return Zo2HelperPath::getPath($path);
                    }
                }
            }
            return false;
        }

        /**
         * Function build all development assets file into Zo2Assets class
         */
        public function buildAssets() {
            if (isset($this->_assets->build->core->less))
                $this->_buildAssets($this->_assets->build->core->less, CORE, 'less');
            if (isset($this->_assets->build->core->js))
                $this->_buildAssets($this->_assets->build->core->js, CORE, 'js');
            if (isset($this->_assets->build->template->less))
                $this->_buildAssets($this->_assets->build->template->less, TEMPLATE, 'less');
            if (isset($this->_assets->build->template->js))
                $this->_buildAssets($this->_assets->build->template->js, TEMPLATE, 'js');
        }

        /**
         * @param $assets
         * @param $position
         * @param $type
         *
         * Do build asset file
         */
        private function _buildAssets($assets, $position, $type) {
            if (count($assets) > 0) {
                foreach ($assets as $inputName => $outputName) {
                    $typePath = $type;
                    if ($type == 'less') {
                        $typePath = 'css';
                    }
                    if ($position == CORE) {
                        $inputFile = Zo2HelperPath::getZo2FilePath('assets/zo2/development/' . $type . '/' . $inputName, 'path');
                        $outputFile = Zo2HelperPath::getZo2FilePath('assets/zo2/' . $typePath . '/' . $outputName, 'path');
                    } elseif ($position == TEMPLATE) {
                        $inputFile = Zo2HelperPath::getTemplateFilePath('assets/zo2/development/' . $type . '/' . $inputName, 'path');
                        $outputFile = Zo2HelperPath::getTemplateFilePath('assets/zo2/' . $typePath . '/' . $outputName, 'path');
                    }

                    if ($type == 'less') {
                        Zo2HelperCompiler::less($inputFile, $outputFile);
                    } elseif ($type == 'js') {
                        Zo2HelperCompiler::javascript($inputFile, $outputFile);
                    }
                }
            }
        }

        /**
         * Main function use to load all assets into Zo2Assets class than ready render into document
         */
        public function loadAssets() {
            if (Zo2Framework::isSite()) {
                foreach ($this->_assets->load->frontend as $name => $asset) {
                    $this->_loadAsset($asset);
                    if ($name == 'bootstrap.3') {
                        /* RTL */
                        if (Zo2Framework::get('enable_rtl') == 1 && JFactory::getDocument()->direction == 'rtl')
                            $this->addStyleSheet('vendor/bootstrap/addons/bootstrap-rtl/css/bootstrap-rtl.css');
                    }
                }
                if (Zo2Framework::get('enable_custom_css', 1) == 1)
                    $this->addStyleSheet('zo2/css/custom.css');
            } else {
                foreach ($this->_assets->load->backend as $asset) {
                    $this->_loadAsset($asset);
                }
            }
        }

        private function _loadAsset($asset) {
            if (isset($asset->joomla)) {
                foreach ($asset->joomla as $version) {
                    if ($this->_isMatchJVersion($version)) {
                        if (isset($asset->paths)) {
                            foreach ($asset->paths as $path) {
                                $ext = strtolower(JFile::getExt($path));
                                switch ($ext) {
                                    case "css":
                                        $this->addStyleSheet($path);
                                        break;
                                    case "js":
                                        $this->addScript($path);
                                        break;
                                }
                            }
                        }
                    }
                }
            } else {
                /* Load in all Joomla! */
                if (isset($asset->paths)) {
                    foreach ($asset->paths as $path) {
                        $ext = strtolower(JFile::getExt($path));
                        switch ($ext) {
                            case "css":
                                $this->addStyleSheet($path);
                                break;
                            case "js":
                                $this->addScript($path);
                                break;
                        }
                    }
                }
            }
        }

        /**
         *
         * @param type $file
         * @return \Zo2Assets
         */
        public function addStyleSheet($file) {
            $assetFile = $this->getAssetFile($file);
            if ($assetFile) {
                $this->_stylesheets[$assetFile] = $assetFile;
            }
            return $this;
        }

        /**
         * @todo Should we allow add less ?
         * @param type $style
         * @param bool $less
         * @return \Zo2Assets
         */
        public function addStyleSheetDeclaration($style, $less = false) {
            if ($less) {
                $style = Zo2HelperCompiler::lessStyle($style);
            }
            $this->_stylesheetDeclarations[] = $style;
            return $this;
        }

        /**
         * @param type $file
         *
         * @return \Zo2Assets
         */
        public function addScript($file) {
            $assetFile = $this->getAssetFile($file);
            if ($assetFile != false) {
                $this->_javascripts[$assetFile] = $assetFile;
            }
            return $this;
        }

        /**
         *
         * @param type $script
         * @return \Zo2Assets
         */
        public function addScriptDeclaration($script) {
            $this->_javascriptDeclarations[] = $script;
            return $this;
        }

        /**
         * Do build development script into production
         */
        public function buildFrameworkProduction() {
            /* This method only need call one time */
            static $called = false;
            $templateName = Zo2Framework::getTemplateName();
            if ($called === false && !empty($templateName) && Zo2Framework::isZo2Template()) {
                /**
                 * @todo move these list into config file
                 */
                $lessFiles['core'][] = 'adminmegamenu';
                $lessFiles['core'][] = 'megamenu-responsive';
                $lessFiles['core'][] = 'megamenu';
                $lessFiles['core'][] = 'shortcodes';
                $lessFiles['core'][] = 'social';
                $jsFiles['core'][] = 'adminlayout';
                $jsFiles['core'][] = 'adminmegamenu';
                $jsFiles['core'][] = 'adminsocial';
                $jsFiles['core'][] = 'assets';
                $jsFiles['core'][] = 'megamenu';
                $jsFiles['core'][] = 'shortcodes';
                $jsFiles['core'][] = 'social';
                $jsFiles['core'][] = 'socialshare';
                $cssFiles['core'] = array();
                $cssFiles['template'] = array();

                /* Template */

                $templateAssetsPath = Zo2HelperPath::getTemplateFilePath('layouts/assets.json');
                $templatePresetsPath = Zo2HelperPath::getTemplateFilePath('layouts/presets.json');

                $templateAssets = json_decode(file_get_contents($templateAssetsPath), true);
                $templatePresets = json_decode(file_get_contents($templatePresetsPath), true);

                foreach ($templateAssets as $asset) {
                    if ($asset['type'] == 'js')
                        $jsFiles['template'][] = $asset['path'];
                    else if ($asset['type'] == 'less')
                        $lessFiles['template'][] = $asset['path'];
                    else if ($asset['type'] == 'css')
                        $cssFiles['template'][] = $asset['path'];
                }

                foreach ($templatePresets as $preset) {
                    if (isset($preset['less']) && !empty($preset['less']))
                        $lessFiles['template'][] = $preset['less'];
                }

                //$lessFiles['template'] = '';
                //$jsFiles['template'] = '';

                $buildProduction = Zo2Framework::get('build_production', 1);


                switch ($buildProduction) {
                    case 1: /* Clear cache and rebuild everything */
                        /* Do build core less */
                        foreach ($lessFiles['core'] as $lessFile) {
                            $input = $this->getPath($this->get('zo2Root') . '/assets/zo2/development/less/' . $lessFile . '.less');
                            $output = $this->getPath($this->get('zo2Root') . '/assets/zo2/css/' . $lessFile . '.css');
                            $this->_buildLess($input, $output);
                        }
                        if (isset($lessFiles['template']) && is_array($lessFiles['template'])) {
                            /* Do build template less */
                            foreach ($lessFiles['template'] as $lessFile) {
                                $input = $this->getPath($this->get('siteTemplate') . '/assets/zo2/development/less/' . $lessFile . '.less');
                                $output = $this->getPath($this->get('siteTemplate') . '/assets/zo2/css/' . $lessFile . '.css');
                                $this->_buildLess($input, $output);
                            }
                        }

                        /* Do build core css */
                        foreach ($cssFiles['core'] as $cssFile) {
                            $input = $this->getPath($this->get('zo2Root') . '/assets/zo2/development/css/' . $cssFile . '.css');
                            $output = $this->getPath($this->get('zo2Root') . '/assets/zo2/css/' . $cssFile . '.css');
                            $this->_buildCss($input, $output);
                        }
                        if (isset($cssFiles['template']) && is_array($cssFiles['template'])) {
                            /* Do build template less */
                            foreach ($cssFiles['template'] as $cssFile) {
                                $input = $this->getPath($this->get('siteTemplate') . '/assets/zo2/development/css/' . $cssFile . '.css');
                                $output = $this->getPath($this->get('siteTemplate') . '/assets/zo2/css/' . $cssFile . '.css');
                                $this->_buildCss($input, $output);
                            }
                        }

                        /* Do build core js */
                        foreach ($jsFiles['core'] as $jsFile) {
                            $input = $this->getPath($this->get('zo2Root') . '/assets/zo2/development/js/' . $jsFile . '.js');
                            $output = $this->getPath($this->get('zo2Root') . '/assets/zo2/js/' . $jsFile . '.js');
                            $this->_buildJs($input, $output);
                            //JFactory::getApplication()->enqueueMessage('Working: ' . $jsFilePath);
                        }
                        if (isset($jsFiles['template']) && is_array($jsFiles['template'])) {
                            /* Do build template js */
                            foreach ($jsFiles['template'] as $jsFile) {
                                $input = $this->getPath($this->get('siteTemplate') . '/assets/zo2/development/js/' . $jsFile . '.js');
                                $output = $this->getPath($this->get('siteTemplate') . '/assets/zo2/js/' . $jsFile . '.js');
                                $this->_buildJs($input, $output);
                                //JFactory::getApplication()->enqueueMessage('Working: ' . $jsFilePath);
                            }
                        }
                        break;
                }
                $called = true;
            }
        }

        private function _buildLess($input, $output) {
            $cleanProduction = Zo2Framework::get('clean_production', 1);
            /* Do clean old files */
            if (JFile::exists($output) && $cleanProduction)
                JFile::delete($output);
            if (!is_file($output) || filemtime($input) > filemtime($output)) {
                if (Zo2HelperCompiler::less($input, $output)) {
                    //JFactory::getApplication()->enqueueMessage('Success: ' . $cssFilePath);
                }
            } else {
                //JError::raiseNotice(100, 'File exists: ' . $cssFilePath);
            }
            /**
             * @todo css compress
             */
        }

        private function _buildJs($input, $output) {
            $cleanProduction = Zo2Framework::get('clean_production', 1);
            if (JFile::exists($output) && $cleanProduction)
                JFile::delete($output);
            if (!is_file($output) || filemtime($input) > filemtime($output)) {
                if (Zo2HelperCompiler::javascript($input, $output)) {
                    //JFactory::getApplication()->enqueueMessage('Success: ' . $jsFilePathOutput);
                }
            } else {
                //JError::raiseNotice(100, 'File exists: ' . $jsFilePathOutput);
            }
        }

        private function _buildCss($input, $output) {
            $cleanProduction = Zo2Framework::get('clean_production', 1);
            if (JFile::exists($output) && $cleanProduction)
                JFile::delete($output);
            if (!is_file($output) || filemtime($input) > filemtime($output)) {
                if (Zo2HelperCompiler::styleSheet($input, $output)) {
                    //JFactory::getApplication()->enqueueMessage('Success: ' . $jsFilePathOutput);
                }
            } else {
                //JError::raiseNotice(100, 'File exists: ' . $jsFilePathOutput);
            }
        }

        public function generateAssets($type) {
            $combineJs = Zo2Framework::get('combine_js');
            $combineCss = Zo2Framework::get('combine_css');
            /* Generate javascript */
            if ($type == 'js') {
                $jsHtml = '';
                /* Do compress */
                if ($combineJs) {
                    $jsName = 'cache/script.combined.js';
                    $jsFilePath = Zo2HelperPath::getTemplateFilePath('assets/' . $jsName);
                    $jsContent = '';
                    if (!file_exists($jsFilePath)) {
                        foreach ($this->_javascripts as $javascript => $path) {
                            $jsContent .= file_get_contents($path) . "\n";
                        }
                        file_put_contents($jsFilePath, $jsContent);
                    }
                    $jsHtml .='<script type="text/javascript" src="' . rtrim(JUri::root(true), '/') . '/' . $jsFile . '"></script>';
                } else {
                    foreach ($this->_javascripts as $javascript => $path) {
                        $jsHtml .='<script type="text/javascript" src="' . rtrim(JUri::root(true), '/') . '/' . $javascript . '"></script>';
                    }
                }
                $jsDeclarationHtml = '<script>jQuery(document).ready( function () {';
                foreach ($this->_javascriptDeclarations as $javascriptDeclaration) {
                    $jsDeclarationHtml .= $javascriptDeclaration;
                }
                $jsDeclarationHtml .= ' }); </script>';
                return $jsHtml . "\n" . $jsDeclarationHtml;
            } else {
                $cssHtml = '';
                if ($combineCss) {
                    $cssName = 'cache/style.combined.css';
                    $cssFilePath = Zo2HelperPath::getTemplateFilePath('assets/' . $cssName);
                    $cssUri = rtrim(JUri::root(true), '/') . '/' . $cssFile;
                    if (!file_exists($cssFilePath)) {
                        $cssContent = '';
                        foreach ($this->_stylesheets as $styleSheets => $path) {
                            $currentCssContent = file_get_contents($path);
                            $currentCssContent = CssMinifier::minify($currentCssContent);
                            $currentCssContent = Zo2HelperAssets::fixCssUrl($currentCssContent, $cssUri, '/' . $styleSheets);
                            $cssContent .= $currentCssContent . "\n";
                        }
                        $cssContent = Zo2HelperAssets::moveCssImportToBeginning($cssContent);
                        file_put_contents($cssFilePath, $cssContent);
                    }
                    $cssHtml .='<link rel="stylesheet" href="' . $cssUri . '"></script>';
                } else {
                    foreach ($this->_stylesheets as $styleSheets => $path) {
                        $cssHtml .= '<link rel="stylesheet" href="' . rtrim(JUri::root(true), '/') . '/' . $styleSheets . '">';
                    }
                }
                $cssDeclarationHtml = '<style>';
                foreach ($this->_stylesheetDeclarations as $stylesheetDeclaration) {
                    $cssDeclarationHtml .= $stylesheetDeclaration;
                }
                $cssDeclarationHtml .= '</style>';
                return $cssHtml . "\n" . $cssDeclarationHtml;
            }
        }

    }

}