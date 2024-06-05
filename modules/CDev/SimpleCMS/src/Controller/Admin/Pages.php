<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\Core\Database;
use CDev\SimpleCMS\Model\Page;
use CDev\Sale\Model\SaleDiscount;

class Pages extends \XLite\Controller\Admin\AAdmin
{
    protected $params = ['target', 'page'];

    /**
     * @return bool
     */
    public function checkACL()
    {
        $page = Request::getInstance()->page;

        return parent::checkACL()
            || (
                Auth::getInstance()->isPermissionAllowed('manage custom pages')
                && $page === 'primary'
            )
            || (
                Auth::getInstance()->isPermissionAllowed('manage menus')
                && in_array($page, ['menus_P', 'menus_F'], true)
            );
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $isAllowedCustomPagesEditing = Auth::getInstance()->isPermissionAllowed('manage custom pages');
        $isAllowedMenusEditing = Auth::getInstance()->isPermissionAllowed('manage menus');
        if ($isAllowedCustomPagesEditing && !$isAllowedMenusEditing) {
            return static::t('Content pages');
        } elseif ($isAllowedMenusEditing && !$isAllowedCustomPagesEditing) {
            return static::t('Menus');
        }

        return static::t('Menus & Pages');
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return Request::getInstance()->page ?? Page::TYPE_PRIMARY;
    }

    /**
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        if (Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)) {
            $list['primary'] = static::t('Catalog pages');
            $list['content'] = static::t('Content pages');
        } elseif (Auth::getInstance()->isPermissionAllowed('manage custom pages')) {
            $list['primary'] = static::t('Content pages');
        }

        if (Auth::getInstance()->isPermissionAllowed('manage menus')) {
            $list['menus_P'] = static::t('Primary menu');
            $list['menus_F'] = static::t('Footer menu');
        }

        return $list;
    }

    /**
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if (Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)) {
            $list['primary'] = 'modules/CDev/SimpleCMS/tabs/primary_pages.twig';
            $list['content'] = 'modules/CDev/SimpleCMS/tabs/content_pages.twig';
        } else {
            $list['primary'] = 'modules/CDev/SimpleCMS/tabs/content_pages.twig';
        }

        $list['menus_P'] = 'modules/CDev/SimpleCMS/menus/body.twig';
        $list['menus_F'] = 'modules/CDev/SimpleCMS/menus/body.twig';

        return $list;
    }

    /**
     * Check if the option "Show default menu along with the custom one" is displayed
     *
     * @return bool
     */
    public function isVisibleShowDefaultOption()
    {
        return false;
    }

    /**
     * 'updateSalePages' action handler.
     * @throws \Exception
     */
    protected function doActionUpdateSalePages()
    {
        $data          = (Request::getInstance()->getData()['data'] ?? []);
        $repo          = Database::getRepo('CDev\Sale\Model\SaleDiscount');
        $entityManager = Database::getEM();
        if (empty($repo) === false) {
            foreach ($data as $id => $item) {
                /** @var SaleDiscount $dbItem */
                $dbItem = $repo->findOneBy([ 'id' => $id ]);
                if (!empty($dbItem)) {
                    $dbItem->setEnabled(!empty($item['enabled']));
                    $entityManager->persist($dbItem);
                }
            }
        }
        $entityManager->flush();
    }
}
