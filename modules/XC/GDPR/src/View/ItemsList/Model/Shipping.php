<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\ItemsList\Model;

use XCart\Extender\Mapping\ListChild;
use XC\GDPR\Model\Activity;
use XC\GDPR\Model\Repo\Activity as ActivityRepo;

/**
 * @ListChild(list="gdpr-activities", zone="admin", weight="4000")
 */
class Shipping extends \XC\GDPR\View\ItemsList\Model\AActivity
{
    protected function getMainHeadTitle()
    {
        return static::t('Gdpr Shippings');
    }

    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $cnd->{ActivityRepo::PARAM_TYPE} = Activity::TYPE_SHIPPING;

        return $cnd;
    }

    protected function getDescriptionColumnValue(Activity $activity)
    {
        $details = $activity->getDetails();

        return !empty($details['name'])
            ? $details['name']
            : parent::getDescriptionColumnValue($activity);
    }
}
