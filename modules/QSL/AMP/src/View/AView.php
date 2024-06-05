<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\URLManager;
use QSL\AMP\Core\AMPDetectorTrait;

/**
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    use AMPDetectorTrait;

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        if (static::isAMP()) {
            $params = array_merge($params, ['amp_widget_cache_key']);
        }

        return $params;
    }

    /**
     * Get list of methods, priorities and interfaces for the resources
     *
     * @return array
     */
    protected static function getResourcesSchema()
    {
        if (!static::isAMP()) {
            return parent::getResourcesSchema();
        }

        return [
            ['getAMPResources', 100, null, null],
        ];
    }

    /**
     * Return list of widget resources
     *
     * @return array
     */
    protected function getAMPResources()
    {
        return [
            static::RESOURCE_CSS => $this->getAmpCSSFiles(),
            static::RESOURCE_JS  => $this->getAmpComponentResources(),
        ];
    }

    /**
     * @return array
     */
    private function getAmpComponentResources()
    {
        return array_map(static function ($component) {
            return [
                'url' => "https://cdn.ampproject.org/v0/$component-0.1.js",
                'amp-component' => $component,
            ];
        }, $this->getAmpComponents());
    }

    /**
     * Register required amp components in the descendant classes in the form of ['amp-form', ...]
     *
     * @return array
     */
    protected function getAmpComponents()
    {
        return [];
    }

    /**
     * Display plain array as JS array
     *
     * @param array $data Plain array
     *
     * @return void
     */
    public function displayCommentedData(array $data)
    {
        if (static::isAMP()) {
            $data = [];
        }
        if (!empty($data)) {
            echo('<script type="text/x-cart-data">' . "\r\n" . json_encode($data) . "\r\n" . '</script>' . "\r\n");
        }
    }

    /**
     * AMP-mode styles
     *
     * NOTE: Use this method instead of getCSSFiles for AMP page styles
     *
     * .less files are merged with modules/QSL/AMP/styles/initialize.less by default
     *
     * @return array
     */
    protected function getAmpCSSFiles()
    {
        return [
            [
                'file' => 'modules/QSL/AMP/styles/initialize.less',
                'media' => 'force_all',
            ],
            [
                'file' => 'modules/QSL/AMP/styles/base.less',
            ],
        ];
    }

    /**
     * Construct absolute url from relative path
     *
     * @param $relative
     *
     * @return string
     */
    public function getAbsoluteURL($relative)
    {
        return URLManager::getShopURL($relative, null, [], null, null, true);
    }

    /**
     * @return bool
     */
    protected function isAmpMainProductList()
    {
        return false;
    }
}
