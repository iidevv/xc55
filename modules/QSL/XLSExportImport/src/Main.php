<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport;

/**
 * Main module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Check ZipArchive class is exists or not
     *
     * @return boolean
     */
    public static function hasZipArchive()
    {
        return (bool)class_exists('ZipArchive', false);
    }

    /**
     * Check XMLReader extension is installed or not
     *
     * @return boolean
     */
    public static function hasXMLReader()
    {
        return (bool)class_exists('XMLReader', false);
    }
}
