<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->addRelatedTarget('pages', 'pages', ['page' => 'content'], ['page' => 'primary']);
        $this->addRelatedTarget('pages', 'pages', ['page' => 'menus_P'], ['page' => 'primary']);
        $this->addRelatedTarget('pages', 'pages', ['page' => 'menus_F'], ['page' => 'primary']);

        $this->addRelatedTarget('menus', 'pages', [], ['page' => 'primary']);
        $this->addRelatedTarget('page', 'pages', [], ['page' => 'primary']);
        $this->addRelatedTarget('front_page', 'pages', [], ['page' => 'primary']);
        $this->addRelatedTarget('banner_rotation', 'pages', [], ['page' => 'primary']);
        $this->addRelatedTarget('featured_products', 'pages', ['page' => 'front_page'], ['page' => 'primary']);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (isset($items['store_design'][static::ITEM_CHILDREN])) {
            $items['store_design'][static::ITEM_CHILDREN]['logo_favicon'] = [
                static::ITEM_TITLE      => static::t('Logo & Icons'),
                static::ITEM_TARGET     => 'logo_favicon',
                static::ITEM_PERMISSION => \XLite\Model\Role\Permission::ROOT_ACCESS,
                static::ITEM_WEIGHT     => 140,
            ];

            $isAllowedCustomPagesEditing = Auth::getInstance()->isPermissionAllowed('manage custom pages');
            $isAllowedMenusEditing = Auth::getInstance()->isPermissionAllowed('manage menus');
            if ($isAllowedCustomPagesEditing || $isAllowedMenusEditing) {
                $menusPagesItemTitle = $isAllowedCustomPagesEditing
                    ? ($isAllowedMenusEditing ? static::t('Menus & Pages') : static::t('Content pages'))
                    : static::t('Menus');

                $items['store_design'][static::ITEM_CHILDREN]['pages'] = [
                    static::ITEM_TITLE      => $menusPagesItemTitle,
                    static::ITEM_TARGET     => 'pages',
                    static::ITEM_EXTRA      => [
                        'page' => ($isAllowedCustomPagesEditing ? 'primary' : 'menus_P')
                    ],
                    static::ITEM_PERMISSION => ['manage custom pages', 'manage menus'],
                    static::ITEM_WEIGHT     => 150,
                ];
            }
        }

        if (Auth::getInstance()->hasRootAccess()) {
            unset($items['store_design'][static::ITEM_CHILDREN]['front_page']);
        }

        return $items;
    }
}
