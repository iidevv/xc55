<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View;

use XCart\Extender\Mapping\Extender as Extender;
use XLite\Controller\Customer\Category;
use XLite\Controller\Customer\Main;
use XLite\Controller\Customer\Product;

/**
 * Decorate AView to add js/css files
 *
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    /**
     * Tool core class
     *
     * @var null|false|object
     *
     */
    protected static $magic360 = null;

    /**
     * Magic360 enable flag
     *
     * @var bool
     *
     */
    protected static $isSkinActMagicImagesEnabled = null;

    /**
     * Product's spin data
     *
     * @var array
     *
     */
    protected static $magicSetSpinData = [];

    /**
     * Method to check if product has spin
     *
     * @param object $product Product
     *
     * @return boolean
     */
    public static function hasProductSpin($product = null)
    {
        if (!$product) {
            return false;
        }

        $magicSwatchesSet = $product->getMagicSwatchesSet();

        $id = $magicSwatchesSet[0] ? $magicSwatchesSet[0]->getId() : 0;

        if (!$id) {
            return false;
        }

        if (!isset(static::$magicSetSpinData[$id])) {
            static::$magicSetSpinData[$id] = $magicSwatchesSet[0]->hasSpin();
            if (!static::$magicSetSpinData[$id]) {
                //NOTE: old way
                $config = static::getProductsSpinConfig();
                if ($config && ($config['all-ids'] || in_array($id, $config['ids']))) {
                    $images      = $magicSwatchesSet[0]->getImages()->toArray();
                    $imagesCount = count($images);
                    if ($imagesCount && $imagesCount >= $config['columns']) {
                        static::$magicSetSpinData[$id] = true;
                    }
                }
            }
        }

        return static::$magicSetSpinData[$id];
    }

    /**
     * Method to get products spin old config
     *
     * @return array|false
     */
    public static function getProductsSpinConfig()
    {
        static $config = null;
        $allIds = true;

        if ($config === null) {
            $config  = false;
            $tool    = static::getToolObj('SkinActMagicImages');
            $ids     = $tool->params->getValue('product-ids', 'product');
            $ids     = is_string($ids) ? trim($ids) : '';
            $columns = $tool->params->getValue('columns', 'product');
            $columns = is_string($columns) ? trim($columns) : '';
            if (!empty($ids) && !empty($columns)) {
                if ($ids == 'all') {
                    $ids = [];
                } else {
                    $allIds == false;
                    $ids = explode(',', $ids);
                }
                if ($allIds || !empty($ids)) {
                    $columns = intval($columns);
                    if ($columns) {
                        $config = [
                            'all-ids' => $allIds,
                            'ids'     => $ids,
                            'columns' => $columns,
                        ];
                    }
                }
            }
        }

        return $config;
    }

    /**
     * Method to get tool core class
     *
     * @param string $toolId Tool id
     *
     * @return false|object
     */
    public static function getToolObj($toolId)
    {
        $class = "\\Qualiteam\\{$toolId}\\Classes\\Helper";
        if (class_exists($class)) {
            $tool = $class::getInstance()->getPrimaryTool();
        }

        return $tool;
    }

    /**
     * Get JS files list
     *
     * @return array
     */
    public function getJSFiles()
    {
        static $list = null;

        if ($list === null) {
            $list = [];
            if (!\XLite::isAdminZone() && static::shouldIncludeHeaders('SkinActMagicImages')) {
                $list[] = 'modules/Qualiteam/SkinActMagicImages/js/magic360.js';
            }
        }

        return array_merge(
            parent::getJSFiles(),
            $list
        );
    }

    /**
     * Method to check if headers should be included
     *
     * @param string $toolId Tool id
     *
     * @return boolean
     */
    public static function shouldIncludeHeaders($toolId)
    {
        $tool = static::getToolObj($toolId);

        return $tool && ($tool->params->checkValue('include-headers-on-all-pages', 'Yes') || static::isToolEnabled($toolId));
    }

    /**
     * Method to check if tool is enabled
     *
     * @param string $toolId Tool id
     *
     * @return boolean
     */
    public static function isToolEnabled($toolId)
    {
        if (!isset(static::$isSkinActMagicImagesEnabled)) {
            if (\XLite::isAdminZone()) {
                static::$isSkinActMagicImagesEnabled = false;

                return static::$isSkinActMagicImagesEnabled;
            }
            $tool              = static::getToolObj($toolId);
            $page              = static::getCurrentPageType();
            static::$isSkinActMagicImagesEnabled = $tool && $tool->params->profileExists($page) && $tool->params->checkValue('enable-effect', 'Yes', $page);
        }

        return static::$isSkinActMagicImagesEnabled;
    }

    /**
     * Method to get current page
     *
     * @return string
     */
    public static function getCurrentPageType()
    {
        static $page = null;

        if ($page === null) {
            $controller = \XLite::getController();
            if ($controller instanceof Main) {
                $page = 'homepage';
            } elseif ($controller instanceof Category) {
                $page = 'category';
            } elseif ($controller instanceof Product) {
                $page = 'product';
            } else {
                $page = '';
            }
        }

        return $page;
    }

    /**
     * Get CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        static $list = null;

        if ($list === null) {
            $list = [];
            if (!\XLite::isAdminZone() && static::shouldIncludeHeaders('SkinActMagicImages')) {
                $list[] = 'modules/Qualiteam/SkinActMagicImages/css/magic360.css';
                $list[] = 'modules/Qualiteam/SkinActMagicImages/css/magic360.module.css';
            }
        }

        return array_merge(
            parent::getCSSFiles(),
            $list
        );
    }
}
