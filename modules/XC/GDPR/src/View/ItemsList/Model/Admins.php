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
 * @ListChild(list="gdpr-activities", zone="admin", weight="2000")
 */
class Admins extends \XC\GDPR\View\ItemsList\Model\AActivity
{
    protected function getMainHeadTitle()
    {
        return static::t('Gdpr Admins');
    }

    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $cnd->{ActivityRepo::PARAM_TYPE} = Activity::TYPE_ADMIN;

        return $cnd;
    }

    protected function getDescriptionColumnValue(Activity $activity)
    {
        $result = '';
        $details = $activity->getDetails();

        if (!empty($details['login'])) {
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Profile');
            if (!empty($details['id']) && $repo->find((int)$details['id'])) {
                $url = $this->buildURL('profile', '', [
                    'profile_id' => $details['id']
                ]);
                $result = "<a href='{$url}'>{$details['login']}</a>";
            } else {
                $result = $details['login'];
            }
        }

        return $result ?: parent::getDescriptionColumnValue($activity);
    }
}
