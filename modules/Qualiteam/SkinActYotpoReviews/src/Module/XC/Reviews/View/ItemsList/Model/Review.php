<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\View\ItemsList\Model\Review
{
    protected function isHeaderVisible()
    {
        return false;
    }

    protected function isRemoved()
    {
        return false;
    }

    protected function isSelectable()
    {
        return false;
    }

    protected function getRightActions()
    {
        return [];
    }

    protected function defineColumns()
    {
        $list = parent::defineColumns();
        return $this->getChangedColumns($list);
    }

    protected function getPanelClass()
    {
        return null;
    }

    protected function getChangedColumns(array $columns): array
    {
        $result = [];

        foreach ($columns as $name => $column) {
            if (!in_array($name, $this->getRemovedColumnNames(), true)) {
                $result[$name] = $column;
            }
        }

        return $result;
    }

    protected function getRemovedColumnNames(): array
    {
        return [
            'status'
        ];
    }

    protected function getBlankItemsListDescription()
    {
        return static::t('SkinActYotpoReviews itemslist.admin.review.blank');
    }
}