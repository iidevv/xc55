<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Core;

/**
 * Reader
 */
class Reader extends \XLite\Base
{
    /**
     * Check filepath allowed
     *
     * @param string $path File path
     *
     * @return boolean
     */
    public static function isAllowedPath($path)
    {
        return (bool)preg_match(
            '/\.(?:' . implode('|', \QSL\XLSExportImport\Core\Writer::getExtensions()) . ')$/Ss',
            $path
        );
    }
}
