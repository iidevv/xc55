<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced\Logic\FeedGenerator\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Products extends \XC\GoogleFeed\Logic\Feed\Step\Products
{

    /**
     * @param \XLite\Model\Product $model
     * @return array
     */
    protected function getProductRecord(\XLite\Model\Product $model)
    {
        $result = parent::getProductRecord($model);

        $result['g:description'] = preg_replace('/[[:cntrl:]]/S', '', mb_substr(trim(strip_tags($model->getProcessedBriefDescription())), 0, self::DESCRIPTION_LENGTH));

        return $result;

    }
}