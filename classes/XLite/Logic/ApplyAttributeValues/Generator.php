<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\ApplyAttributeValues;

/**
 * Quick data generator
 */
class Generator extends \XLite\Logic\AGenerator
{
    /**
     * Return memberships
     *
     * @return \XLite\Model\Membership[]
     */
    public function getAttrsDiff()
    {
        return $this->getOptions()->attrsDiff;
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return [
            'XLite\Logic\ApplyAttributeValues\Step\Products',
        ];
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'applyAttributeValues';
    }
}
