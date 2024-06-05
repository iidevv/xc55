<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\FormField\Select;

/**
 * Review type selection widget
 */
class ReviewType extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            '' => static::t('Reviews and ratings'),
            \XC\Reviews\Model\Repo\Review::SEARCH_TYPE_RATINGS_ONLY => static::t('Ratings only'),
            \XC\Reviews\Model\Repo\Review::SEARCH_TYPE_REVIEWS_ONLY => static::t('Reviews only'),
        ];
    }
}
