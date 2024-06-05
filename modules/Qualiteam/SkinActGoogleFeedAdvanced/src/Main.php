<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced;

use XLite\Core\Database;
use XLite\Core\Translation;
use XLite\Model\Attribute;

class Main extends \XLite\Module\AModule
{
    public static function getGoogleAttributeLabelTranslate(string $attributeName): string
    {
        return Translation::lbl("SkinActGoogleFeedAdvanced google {$attributeName}")->translate();
    }

    public static function hasGoogleAttribute($configName): bool
    {
        $id = null;
        $value = @unserialize(\XLite\Core\Config::getInstance()->QSL->ProductFeeds->{$configName})[0];

        if ($value) {
            if (preg_match('/^attr:([\d]+)$/', $value, $matches)) {
                $id = $matches[1];
            }
        }

        return $id !== null;
    }

    public static function getGoogleAttributes(): array
    {
        return [
            'googleshop_condition_field',
            'googleshop_gtin_field',
            'googleshop_brand_field',
            'googleshop_googlecategory_field',
            'googleshop_producttype_field',
        ];
    }

    protected static array $attributesByName = [];

    public static function getAttributeByName(string $configName): ?Attribute
    {
        if (!isset(static::$attributesByName[$configName])) {
            $id        = null;
            static::$attributesByName[$configName] = null;
            $value     = @unserialize(\XLite\Core\Config::getInstance()->QSL->ProductFeeds->{$configName})[0];

            if ($value && preg_match('/^attr:([\d]+)$/', $value, $matches)) {
                $id = $matches[1];
            }

            if ($id) {
                static::$attributesByName[$configName] = Database::getRepo(Attribute::class)->find($id);
            }
        }

        return static::$attributesByName[$configName];
    }
}
