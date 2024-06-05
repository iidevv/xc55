<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\ItemsList\Model;

use CDev\SimpleCMS\Model\Page;

class Content extends \XLite\View\ItemsList\Model\Table
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
                static::COLUMN_TEMPLATE  => 'modules/CDev/SimpleCMS/items_list/cells/layout/content/body.twig',
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

        $result->type = [
            Page::TYPE_DEFAULT,
            Page::TYPE_SERVICE,
        ];

        return $result;
    }

    /**
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('page');
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add new';
    }

    /**
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' pages';
    }

    /**
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'CDev\SimpleCMS\View\StickyPanel\ItemsList\Page';
    }

    /**
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @return array
     */
    public static function getSearchParams()
    {
        return [];
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
}
