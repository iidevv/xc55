<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestFeed\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ABase extends \QSL\ProductFeeds\Logic\FeedGenerator\ABase
{
    protected function getVariantAttributeNames(FeedItem $item)
    {
        $values = [];

        foreach ($item->getVariantsAttributes() as $attribute) {
            $values[] = $attribute->getName();
        }

        return $values;
    }
}