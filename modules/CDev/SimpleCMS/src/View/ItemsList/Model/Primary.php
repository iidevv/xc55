<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\ItemsList\Model;

use CDev\SimpleCMS\Model\Page;
use XLite\Core\Database;
use Includes\Utils\Module\Manager;
use CDev\Sale\Model\SaleDiscount;
use XLite\Core\Layout;

class Primary extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_NAME    => static::t('Page name'),
                static::COLUMN_ORDERBY => 100,
                static::COLUMN_MAIN    => true,
                static::COLUMN_TEMPLATE  => 'modules/CDev/SimpleCMS/items_list/cells/name.twig',
            ],
            'layout' => [
                static::COLUMN_NAME      => static::t('Layout'),
                static::COLUMN_HEAD_HELP => static::t('pages.items-list.layout-help-message', [
                    'url' => \XLite\Core\URLManager::getShopURL()
                ]),
                static::COLUMN_TEMPLATE  => 'modules/CDev/SimpleCMS/items_list/cells/layout/body.twig',
                static::COLUMN_ORDERBY   => 200,
            ],
            'link' => [
                static::COLUMN_NAME     => static::t('Page URL and Preview'),
                static::COLUMN_TEMPLATE => 'modules/CDev/SimpleCMS/items_list/cells/link.twig',
                static::COLUMN_ORDERBY  => 300,
            ],
        ];
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'CDev\SimpleCMS\Model\Page';
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->type = Page::TYPE_PRIMARY;

        return $result;
    }

    /**
     * @return array
     */
    protected function getPageData()
    {
        $salesPages = [];
        $salesPagesRepo = Database::getRepo('CDev\Sale\Model\SaleDiscount');
        if ($salesPagesRepo) {
            $salesPages = $salesPagesRepo->findBy([ 'showInSeparateSection' => true ]);
        }
        $pages = array_filter(parent::getPageData(), static function ($item) {
            /** @var Page $item */
            return !$item->getModule()
                || Manager::getRegistry()->isModuleEnabled($item->getModule());
        });
        $salesPagesAdded = empty($salesPages);
        $result = [];
        foreach ($pages as $page) {
            $result[] = $page;
            if ($page->module === 'CDev-Sale' && !$salesPagesAdded) {
                $result = array_merge($result, $salesPages);
                $salesPagesAdded = true;
            }
        }

        if (!$salesPagesAdded) {
            $result = array_merge($result, $salesPages);
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function getLeftActions()
    {
        $result = parent::getLeftActions();

        foreach ($result as $key => $value) {
            if ($value === 'items_list/model/table/parts/switcher.twig') {
                $result[$key] = 'modules/CDev/SimpleCMS/items_list/model/table/parts/switcher.twig';
            }
        }

        return $result;
    }


    /**
     * Returns true if the current item is an instance of the Sale page (SaleDiscount).
     *
     * @param SaleDiscount | Page $item Sale page or system page.
     *
     * @return boolean
     */
    public function isSalePage($item)
    {
        return ($item instanceof SaleDiscount);
    }

    /**
     * Returns whether the sticky panel is visible on the page
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return true;
    }

    /**
     * Returns the value for the src tag for the layout image in the table.
     *
     * @param SaleDiscount | Page $item
     *
     * @return string
     */
    protected function getLayoutTypeImageUrl($item)
    {
        return (
            ($this->isSalePage($item)) ?
                Layout::getInstance()
                    ->getResourceWebPath('modules/CDev/SimpleCMS/items_list/cells/layout/images/left.svg') :
                $item->getLayoutTypeImageUrl()
        );
    }

    /**
     * Returns the alt text for the layout image in the table.
     *
     * @param SaleDiscount | Page $item An instance of the sale page or the other system page.
     *
     * @return string
     */
    protected function getLayoutType($item)
    {
        return ($this->isSalePage($item) ? 'left' : $item->getLayoutType());
    }
}
