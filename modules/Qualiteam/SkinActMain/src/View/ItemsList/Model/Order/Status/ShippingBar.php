<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View\ItemsList\Model\Order\Status;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * Shipping status bar items list
 * @Extender\Depend ("XC\CustomOrderStatuses")
 */
class ShippingBar extends \XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\AStatus
{
    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return ['shippingStatusesBar'];
    }

    /**
     * isHeaderVisible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Return sort field name for tag
     *
     * @return string
     */
    protected function getSortFieldName()
    {
        return 'newPosition';
    }

    protected function getSortByModeDefault()
    {
        return $this->getSortableDefaultSortBy();
    }

    /**
     * Get sort field
     *
     * @return array
     */
    protected function getSortField()
    {
        return $this->getSortType() == static::SORT_TYPE_INPUT
            ? [
                'class'  => $this->getOrderByWidgetClassName(),
                'name'   => 'newPosition',
                'params' => [],
            ]
            :
            [
                'class'  => $this->getMovePositionWidgetClassName(),
                'name'   => 'newPosition',
                'params' => [],
            ];
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if (!empty($columns['name']['class'])) {
            unset($columns['name']['class']);
        }

        return array_merge(
            $columns,
            [
                'showInStatusesBar' => [
                    static::COLUMN_NAME => static::t('Show in statuses bar'),
                    static::COLUMN_CLASS  => '\XLite\View\FormField\Inline\Input\Checkbox\Switcher\YesNo',
                    static::COLUMN_PARAMS => [],
                    static::COLUMN_ORDERBY  => 300
                ]
            ]
        );
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Mark list as removable
     *
     * @return bool
     */
    protected function isRemoved()
    {
        return false;
    }

    protected function getPage()
    {
        return 'shipping';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Order\Status\Shipping';
    }

    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    protected function validateUpdate()
    {
        $result = parent::validateUpdate();

        if ($result) {
            $data = Request::getInstance()->data;
            if ($data) {
                $countToShow = 0;
                foreach ($data as $row) {
                    if (isset($row['showInStatusesBar']) && $row['showInStatusesBar'] === '1') {
                        $countToShow++;
                    }
                }
                if ($countToShow > \Qualiteam\SkinActMain\Helper\ShippingStatusBar::MAXIMUM_NUMBER_OF_STATUSES) {
                    $this->addPlainErrorMessage('', static::t('SkinActMain Only 4 statuses could be showed in statuses bar'));
                    return false;
                }
            }
        }

        return $result;
    }
}
