<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;

/**
 * Module
 */
abstract class AModule
{
    /**
     * @var Module[]
     */
    protected static $moduleData = [];

    /**
     * @return string
     */
    public static function getId()
    {
        return Module::getModuleIdByClassName(static::class);
    }

    /**
     * @return Module
     */
    public static function getModuleData()
    {
        $id = static::getId();
        if (!isset(static::$moduleData[$id])) {
            static::$moduleData[$id] = Manager::getRegistry()->getModule($id);
        }

        return static::$moduleData[$id];
    }

    /**
     * Method to initialize concrete module instance
     *
     * @return void
     */
    public static function init()
    {
        // Register image sizes
        static::registerImageSizes();
    }

    /**
     * Return link to the module author page
     *
     * @return string
     */
    public static function getAuthorPageURL()
    {
        return '';
    }

    /**
     * Return link to the module page
     *
     * @return string
     */
    public static function getPageURL()
    {
        return '';
    }

    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return static::getModuleData()['type'] === 'payment'
            ? static::getPaymentSettingsForm()
            : null;
    }

    /**
     * Defines the link for the payment settings form
     *
     * @return string
     */
    public static function getPaymentSettingsForm()
    {
        return null;
    }

    /**
     * Return module dependencies
     *
     * @return array
     * @deprecated
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Return list of mutually exclusive modules
     *
     * @return array
     * @deprecated
     */
    public static function getMutualModulesList()
    {
        return [];
    }

    /**
     * Get module version
     *
     * @return string
     */
    public static function getVersion()
    {
        return static::getModuleData()['version'];
    }

    /**
     * Check - module required disabled+redeploy+uninstall (true) or deploy+uninstall (false)
     *
     * @return boolean
     */
    public static function isSeparateUninstall()
    {
        return false;
    }

    /**
     * Returns image sizes
     *
     * @return array
     */
    public static function getImageSizes()
    {
        return [];
    }

    /**
     * Register image sizes
     *
     * If you want to change existing image sizes only once, on module install
     * you should add a record to install.yaml of your module:
     *
     * For example:
     *
     * XLite\Model\ImageSettings:
     *   - { model: XLite\Model\Image\Product\Image, code: Default, width: 123, height: 321 }
     *   - { model: XLite\Model\Image\Category\Image, code: Default, width: 456, height: 654 }
     *
     * @return void
     */
    public static function registerImageSizes()
    {
        $sizes = static::getImageSizes();

        if ($sizes) {
            \XLite\Logic\ImageResize\Generator::addImageSizes($sizes);
        }
    }
}
