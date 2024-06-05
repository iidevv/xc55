<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault;

use Qualiteam\SkinActSkuVault\View\Tabs\SkuVault;
use XLite\Core\Converter;
use XLite\Module\AModule;

class Main extends AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return Converter::buildURL(SkuVault::TAB_GENERAL);
    }

    public static function getFormattedDescription(string $description): string
    {
        return htmlspecialchars_decode(
            strip_tags(
                str_replace(
                    '&nbsp;',
                    ' ',
                    str_replace(
                        "\n",
                        ' ',
                        $description
                    )
                )
            )
        );
    }
}
