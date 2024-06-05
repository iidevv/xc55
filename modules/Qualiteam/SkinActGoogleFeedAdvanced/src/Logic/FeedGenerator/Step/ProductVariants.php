<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced\Logic\FeedGenerator\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend({"XC\ProductVariants", "XC\GoogleFeed"})
 */
class ProductVariants extends \XC\GoogleFeed\Logic\Feed\Step\Products
{
    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return array
     */
    protected function getVariantRecord(\XC\ProductVariants\Model\ProductVariant $model)
    {
        $result = parent::getVariantRecord($model);

        $result['g:description'] = preg_replace('/[[:cntrl:]]/S', '', mb_substr(trim(strip_tags($model->getProduct()->getProcessedBriefDescription())), 0, self::DESCRIPTION_LENGTH));

        return $result;
    }
}