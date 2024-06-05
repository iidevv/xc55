<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\Model\Role\Permission;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class FrontPage extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'front_page';
        $list[] = 'banner_rotation';

        return $list;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible() && !\XLite\Core\Request::getInstance()->id;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = [];

        if (
            Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS)
            || Auth::getInstance()->isPermissionAllowed('manage front page')
        ) {
            $tabs['front_page'] = [
                'weight' => 100,
                'title' => static::t('Front page'),
                'template' => 'front_page/body.twig',
            ];
        }

        if (Auth::getInstance()->isPermissionAllowed('manage banners')) {
            $tabs['banner_rotation'] = [
                'weight'   => 200,
                'title'    => static::t('Banner rotation'),
                'template' => 'banner_rotation/body.twig',
            ];
        }

        return $tabs;
    }
}
