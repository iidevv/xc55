<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\Tabs;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\Model\Role\Permission;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class GiftTier extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        if (!\XLite\Core\Request::getInstance()->gift_tier_id) {
            $result[] = 'gift_tier';
        }
        return $result;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = [];
        if (Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS)) {
            $list['gift_tier'] = [
                'weight'     => 100,
                'title'      => static::t('Gift tier'),
                'template'   => 'modules/Qualiteam/SkinActFreeGifts/gift_tier.twig',
            ];
        }

        return $list;
    }
}
