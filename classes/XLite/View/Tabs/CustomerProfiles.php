<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to user profile section
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class CustomerProfiles extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'customer_profiles';

        if (\XLite\Core\Request::getInstance()->section === 'Communications') {
            $list[] = 'memberships';
        }

        return $list;
    }


    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'customer_profiles' => [
                'weight'   => 100,
                'title'    => static::t('All customers'),
                'template' => 'customer_profiles/profiles.twig',
            ],
            'memberships'       => [
                'weight'     => 200,
                'title'      => static::t('Memberships'),
                'template'   => 'customer_profiles/memberships.twig',
                'url_params' => $this->getCurrentTarget() === 'customer_profiles' ? ['section' => 'Communications'] : [],
            ],
        ];
    }
}
