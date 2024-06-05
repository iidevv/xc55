<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Layout;

/**
 * Common resources loader
 *
 * @ListChild (list="admin.center", zone="admin")
 * @ListChild (list="layout.main", zone="customer")
 */
class CommonResources extends \XLite\View\AView
{
    /**
     * Get list of methods, priorities and interfaces for the resources
     *
     * @return array
     */
    protected static function getResourcesSchema()
    {
        return [
            ['getCommonFiles', 50, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON],
            ['getResources', 60, null, null],
            ['getThemeFiles', 70, null, null],
            ['getPrintFiles', 400, null, null],
        ];
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            [
                'file'  => 'bootstrap/css/initialize.less',
                'media' => 'screen',
                'merge' => Layout::MERGE_ROOT,
                'reference' => true
            ],
            [
                'file'  => 'bootstrap/css/bootstrap.less',
                'media' => 'screen',
                'merge' => Layout::MERGE_ROOT
            ],
            [
                'file'  => 'css/style.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less'
            ],
        ];
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        return [
            static::RESOURCE_JS  => [
                [
                    'file'      => 'js/jquery.min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/jquery-migrate.min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/jquery-ui.min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/jquery.ui.touch-punch.min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/jquery.cookie.min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/underscore-min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/underscore.string.min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'bootstrap/js/bootstrap.min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/hash.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/object_hash.js',
                    'no_minify' => true,
                ],
                $this->getValidationEngineLanguageResource(),
                [
                    'file'      => 'js/validationEngine.min/jquery.validationEngine.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/validationEngine.min/custom.validationEngine.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/jquery.mousewheel.min.js',
                    'no_minify' => true,
                ],
                'js/regex-mask-plugin.js',
                'js/common.js',
                'js/xcart.element.js',
                'js/xcart.js',
                'js/xcart.extend.js',
                'js/xcart.controller.js',
                'js/xcart.loadable.js',
                'js/xcart.utils.js',
                'js/lazyload.js',
                'js/json5.min.js',
                'js/xcart.popup.js',
                'js/xcart.popup_button.js',
                'js/xcart.form.js',
                'js/loadCSS.min.js',
                'js/onloadCSS.min.js',
                'js/functionNamePolyfill/Function.name.js',
                [
                    'file'      => 'js/php.min.js',
                    'no_minify' => true,
                ],
                [
                    'file'      => 'js/fallback.min.js',
                    'no_minify' => true,
                ],
                'js/core/amd.js',
                'js/core/translate.js',
                [
                    'file'      => 'js/lazysizes.min.js',
                    'no_minify' => true,
                ],
                'js/tooltip.js',
                'js/popover.js',
            ],
            static::RESOURCE_CSS => [
                'css/normalize.css',
                'ui/jquery-ui.css',
                'css/jquery.mousewheel.css',
                'css/validationEngine.jquery.css',
                'css/font-awesome/font-awesome.min.css',
                [
                    'file'   => 'css/common.less',
                    'media'  => 'screen',
                    'weight' => 0,
                ],
                [
                    'file'   => 'fonts/fonts.less',
                    'media'  => 'screen',
                    'weight' => 1,
                ],
            ],
        ];
    }

    /**
     * Return theme common files
     *
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        return ($adminZone ?? \XLite::isAdminZone())
            ? [
                static::RESOURCE_CSS => [
                    'css/style.css',
                    'css/ajax.css',
                ],
            ]
            : [
                static::RESOURCE_CSS => [
                    'css/theme.css',
                    'css/style.css',
                    'css/ajax.css',
                ],
                static::RESOURCE_JS  => [
                    'js/sticky_footer.js',
                    'js/responsive_navbar.js'
                ],
            ];
    }

    /**
     * Return print common files
     *
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getPrintFiles($adminZone = null)
    {
        return [
            static::RESOURCE_CSS => [
                [
                    'file'  => 'css/print.css',
                    'media' => 'print',
                ],
            ],
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getCommonLessFiles()
    {
        return array_filter($this->getCommonFiles()[static::RESOURCE_CSS], static function ($resource) {
            if (is_array($resource)) {
                if (!isset($resource['file'])) {
                    return false;
                }

                $resource = $resource['file'];
            }

            return preg_match('/\.less$/S', $resource);
        });
    }
}
