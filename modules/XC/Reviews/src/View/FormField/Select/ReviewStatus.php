<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\FormField\Select;

/**
 * Review status selection widget
 */
class ReviewStatus extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            '' => static::t('All statuses'),
            \XC\Reviews\Model\Review::STATUS_PENDING  => static::t('Pending'),
            \XC\Reviews\Model\Review::STATUS_APPROVED => static::t('Published'),
        ];
    }
}
