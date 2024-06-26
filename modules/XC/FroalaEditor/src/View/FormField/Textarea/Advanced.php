<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FroalaEditor\View\FormField\Textarea;

use XC\FroalaEditor\Main;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * https://github.com/xcart/wysiwyg-editor
 * @Extender\Mixin
 */
class Advanced extends \XLite\View\FormField\Textarea\Advanced
{
    public const SCRIPT_TAG_REGEX = '/<script[\s\S]*?>[\s\S]*?<\/script>/i';
    public const STYLE_TAG_REGEX  = '/<style[\s\S]*?>[\s\S]*?<\/style>/i';

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/froala_editor.js';

        return $list;
    }

    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][]  = 'froala-editor/js/froala_editor.pkgd.min.js';
        $list[static::RESOURCE_JS][]  = 'froala-editor/js/froala_editor.activate.js';
        $list[static::RESOURCE_JS][]  = $this->getEditorLanguageResource();
        $list[static::RESOURCE_CSS][] = 'froala-editor/css/froala_editor.pkgd.min.css';

        return $list;
    }

    /**
     * Return resource structure for content editor language file.
     * By default there are several ready-to-use language files from content editor project.
     * The translation module is able to use its own language validation file.
     * It should decorate this method for this case.
     *
     * @return array
     */
    protected function getEditorLanguageResource()
    {
        return [
            'file'      => $this->getEditorLanguageFile(),
            'no_minify' => true,
        ];
    }

    /**
     * Return content editor language file path.
     *
     * @return string
     */
    protected function getEditorLanguageFile()
    {
        return 'froala-editor/js/languages/'
               . $this->getCurrentLanguageCode()
               . '.js';
    }

    /**
     * Gets current language code and fixes it in case of en-GB and similar.
     *
     * @return string
     */
    protected function getCurrentLanguageCode()
    {
        $code = $this->getCurrentLanguage()->getCode();

        switch ($code) {
            case 'en':
                return 'en_gb';

            case 'pt':
                return 'pt_pt';

            case 'zh':
                return 'zh_cn';

            default:
                return $code;
        }
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/css/style.css';

        return $list;
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '/form_field/textarea.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/FroalaEditor';
    }

    /**
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes   = parent::assembleClasses($classes);
        $classes[] = 'fr-instance';

        return $classes;
    }

    /**
     * @return array
     */
    protected function getFroalaConfiguration()
    {
        $config = [
            'language'              => $this->getCurrentLanguageCode(),
            'placeholderText'       => '',
            'iframe'                => true,
            'iframeStyleFiles'      => $this->getIframeStyleFiles(),
            'toolbarSticky'         => true,
            'charCounterCount'      => false,
            'videoAllowedTypes'     => ['webm', 'jpg', 'ogg', 'mp4', 'avi'],
            'videoMaxSize'          => \XLite\Core\Converter::getUploadFileMaxSize(),
            'imageMaxSize'          => \XLite\Core\Converter::getUploadFileMaxSize(),
            'videoUploadURL'        => \XLite\Core\Converter::buildURL(
                'files',
                'upload_from_file',
                [
                    'mode'           => 'json',
                    'type'           => 'video',
                    'url_param_name' => 'link',
                    'register'       => true,
                ]
            ),
            'imageUploadURL'        => \XLite\Core\Converter::buildURL(
                'files',
                'upload_from_file',
                [
                    'mode'           => 'json',
                    'type'           => 'image',
                    'url_param_name' => 'link',
                    'register'       => true,
                ]
            ),
            'imageManagerLoadURL'   => \XLite\Core\Converter::buildURL(
                'files',
                'get_image_manager_list',
                []
            ),
            'imageManagerDeleteURL' => \XLite\Core\Converter::buildURL(
                'files',
                'remove_from_image_manager',
                []
            ),
            'imageUploadParam'      => 'file',
            'requestHeaders'        => [
                'X-Requested-With' => 'XMLHttpRequest',
            ],
            'htmlAllowedTags'       => $this->getAllowedHtmlTags(),
            'htmlAllowedAttrs'      => $this->getAllowedHtmlAttrs(),
            'htmlExecuteScripts'    => false,
            'htmlRemoveTags'        => $this->getHtmlRemoveTags(),
            'htmlDoNotWrapTags'     => $this->getHtmlDoNotWrapTags(),
            'toolbarButtons'        => $this->getFroalaToolbarButtons(),
            'toolbarButtonsMD'      => $this->getFroalaToolbarButtons(),
            'toolbarButtonsSM'      => $this->getFroalaToolbarButtons(),
            'toolbarButtonsXS'      => $this->getFroalaToolbarButtons(),
            'appendToDefault'       => $this->getFroalaAppendConfiguration(),

            // https://www.froala.com/wysiwyg-editor/docs/options#pluginsEnabled
            'pluginsEnabled' => [
                'align',
                'charCounter',
                'codeBeautifier',
                'codeView',
                'colors',
                'draggable',
                'embedly',
                'emoticons',
                'entities',
                'file',
                'fontFamily',
                'fontSize',
                'fullscreen',
                'image',
                'imageManager',
                'inlineStyle',
                'lineBreaker',
                'link',
                'lists',
                'paragraphFormat',
                'paragraphStyle',
                'quickInsert',
                'quote',
                'save',
                'table',
                // 'url', // disable by default
                'video',
                'wordPaste',
            ]
        ];

        if ($this->useCustomColors() && $this->getCustomColors()) {
            $config['colorsBackground'] = $this->getCustomColors();
            $config['colorsText'] = $this->getCustomColors();
            $config['colorsStep'] = 7;
        }

        return $config;
    }

    /**
     * @return bool
     */
    protected function useCustomColors()
    {
        return (bool) \XLite\Core\Config::getInstance()->XC->FroalaEditor->use_custom_colors;
    }

    /**
     * @return array
     */
    protected function getCustomColors()
    {
        $customColors  = [];
        $colorsSetting = \XLite\Core\Config::getInstance()->XC->FroalaEditor->custom_colors;

        if ($colorsSetting) {
            $customColors = explode(',', $colorsSetting);
            $customColors = array_map(static function ($color) {
                return '#' . $color;
            }, $customColors);
        }

        $customColors[] = 'REMOVE';

        return $customColors;
    }

    /**
     * Return list of froala toolbar buttons
     *
     * @return array
     */
    protected function getFroalaToolbarButtons()
    {
        return [
            'fontFamily', 'fontSize',
            '|', 'bold', 'italic', 'underline', 'strikeThrough', 'color', 'clearFormatting', 'print',
            '|', 'paragraphFormat', 'paragraphStyle', 'formatOL', 'formatUL',
            '-', 'align', 'indent', 'outdent', 'insertHR', 'quote',
            '|', 'insertImage', 'insertTable', 'insertLink', 'insertVideo',
            '|', 'undo', 'redo', 'html', 'fullscreen', 'help', 'shortcutsHint'
        ];
    }

    /**
     * Return editor options, which should be append to existing without overwriting
     *
     * @return array
     */
    protected function getFroalaAppendConfiguration()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getHtmlRemoveTags()
    {
        return [];
    }

    protected function getHtmlDoNotWrapTags(): array
    {
        return Main::getModuleSettings()['htmlDoNotWrapTags'] ?? [];
    }

    protected function getAllowedHtmlTags(): array
    {
        $auth = Auth::getInstance();
        $isAdmin = $auth->isAdmin();

        return $isAdmin && $auth->isPermissionAllowed('unfiltered html')
            ? ['.*']
            : Main::getModuleSettings()[$isAdmin ? 'htmlAllowedTagsAdmin' : 'htmlAllowedTags'] ?? [];
    }

    protected function getAllowedHtmlAttrs(): array
    {
        $auth = Auth::getInstance();
        $isAdmin = $auth->isAdmin();

        return $isAdmin && $auth->isPermissionAllowed('unfiltered html')
            ? ['.*']
            : Main::getModuleSettings()[$isAdmin ? 'htmlAllowedAttrsAdmin' : 'htmlAllowedAttrs'] ?? [];
    }

    /**
     * Provides style files for iframe mode. Destined to replicate customer zone style.
     *
     * @return array
     */
    protected function getIframeStyleFiles()
    {
        $styleFiles = [];

        $customer = $this->getCustomerLessStyles();

        if ($customer) {
            $styleFiles[] = $customer;
        }

        $styleFiles = array_merge($styleFiles, $this->getThemeStyles());
        $styleFiles = array_merge($styleFiles, $this->getFroalaEditorStyles());

        return $styleFiles;
    }

    /**
     * Returns compiled customer zone style file url.
     *
     * @return string
     */
    protected function getCustomerLessStyles()
    {
        $lessParser = \XLite\Core\LessParser::getInstance();

        $customerLESS = [
            [
                'file'      => \XLite\Core\Layout::getInstance()->getResourceFullPath('bootstrap/css/bootstrap.less', \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON),
                'media'     => 'screen',
                'weight'    => 0,
                'filelist'  => [
                    'bootstrap/css/bootstrap.less',
                ],
                'interface' => \XLite::INTERFACE_WEB,
                'original'  => 'bootstrap/css/bootstrap.less',
                'url'       => \XLite\Core\Layout::getInstance()->getResourceWebPath('bootstrap/css/bootstrap.less', \XLite\Core\Layout::WEB_PATH_OUTPUT_SHORT, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON),
                'less'      => true,
            ],
            [
                'file'      => \XLite\Core\Layout::getInstance()->getResourceFullPath('css/style.less', \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER),
                'media'     => 'screen',
                'merge'     => 'bootstrap/css/bootstrap.less',
                'filelist'  => [
                    'css/style.less',
                ],
                'interface' => null,
                'original'  => 'css/style.less',
                'url'       => \XLite\Core\Layout::getInstance()->getResourceWebPath('css/style.less', \XLite\Core\Layout::WEB_PATH_OUTPUT_SHORT, \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER),
                'less'      => true
            ],
            [
                'file'      => \XLite\Core\Layout::getInstance()->getResourceFullPath('froala-editor/css/style.less', \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON),
                'media'     => 'screen',
                'weight'    => 0,
                'filelist'  => [
                    'froala-editor/css/style.less',
                ],
                'interface' => \XLite::INTERFACE_WEB,
                'original'  => 'froala-editor/css/style.less',
                'url'       => \XLite\Core\Layout::getInstance()->getResourceWebPath('froala-editor/css/style.less', \XLite\Core\Layout::WEB_PATH_OUTPUT_SHORT, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON),
                'less'      => true,
            ]
        ];

        // Customer LESS files parsing
        $lessParser->setZone('default');
        \XLite\Core\Layout::getInstance()->setInterfaceZone(\XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER);

        $lessParser->setHttp('http');
        $style = $lessParser->makeCSS($customerLESS);
        \XLite\Core\Layout::getInstance()->setInterfaceZone(\XLite::INTERFACE_WEB, \XLite::ZONE_ADMIN);

        if ($style && isset($style['url'])) {
            return $style['url'];
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getThemeStyles()
    {
        $themeFiles = $this->getThemeFiles(false);
        $themeFiles = $themeFiles[static::RESOURCE_CSS];
        $themeFilesCSS = [];

        foreach ($themeFiles as $key => $file) {
            if (!is_array($file)) {
                $path = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    $file,
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    \XLite::INTERFACE_WEB,
                    \XLite::ZONE_CUSTOMER
                );

                if ($path) {
                    $themeFilesCSS[] = $this->getShopURL($path, null, ['t' => LC_START_TIME]);
                }
            }
        }

        return $themeFilesCSS;
    }

    /**
     * Returns specific froala editor styles to be used inside iframe
     *
     * @return array
     */
    protected function getFroalaEditorStyles()
    {
        return [
            $this->getShopURL(
                \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    'froala-editor/css/froala_style.fixed.css',
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    \XLite::INTERFACE_WEB,
                    \XLite::ZONE_COMMON
                ),
                null,
                ['t' => LC_START_TIME]
            ),
            $this->getShopURL(
                \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    'froala-editor/css/froala_style.iframe.css',
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    \XLite::INTERFACE_WEB,
                    \XLite::ZONE_COMMON
                ),
                null,
                ['t' => LC_START_TIME]
            ),
        ];
    }

    /**
     * Get processed value
     *
     * @return string
     */
    protected function getProcessedValue()
    {
        return str_replace(
            \XLite\Model\Base\Catalog::WEB_LC_ROOT,
            htmlentities(\XLite::getInstance()->getShopURL(null)),
            $this->getValue()
        );
    }

    /**
     * Detects any script tags inside the value
     */
    protected function hasScripts()
    {
        return preg_match(static::SCRIPT_TAG_REGEX, $this->getValue())
            || preg_match(static::STYLE_TAG_REGEX, $this->getValue());
    }
}
