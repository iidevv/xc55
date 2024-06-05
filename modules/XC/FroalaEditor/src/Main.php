<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FroalaEditor;

use Includes\Utils\ConfigParser;

/**
 * https://github.com/xcart/wysiwyg-editor (2.5.2)
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('froala_settings');
    }

    public static function getModuleSettings(): array
    {
        return ConfigParser::getOptions(['modules', 'XC-FroalaEditor']) ?? [];
    }
}
