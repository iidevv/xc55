<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\MembershipsQuickData;

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
    public function getMemberships()
    {
        $ids = $this->getOptions()->memberships;

        return $ids
            ? \XLite\Core\Database::getRepo('XLite\Model\Membership')->findByIds($ids)
            : [];
    }

    // {{{ Steps

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return [
            'XLite\Logic\MembershipsQuickData\Step\Products',
        ];
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'membershipsQuickData';
    }

    // }}}
}
