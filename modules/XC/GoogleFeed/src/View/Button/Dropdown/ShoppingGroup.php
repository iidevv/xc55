<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\Button\Dropdown;

use XC\GoogleFeed\Model\Attribute;

/**
 * Order print
 */
class ShoppingGroup extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $position = 0;
        $list = [];

        /** @var \XLite\Model\Order\Status\Payment $status */
        foreach (Attribute::getGoogleShoppingGroups() as $group) {
            $list[$group] = [
                'class' => 'XLite\View\Button\Regular',
                'params'   => [
                    'label'      => static::t($group),
                    'style'      => 'action link list-action',
                    'action'     => 'assignGroup',
                    'formParams' => ['groupToSet' => $group]
                ],
                'position' => $position,
            ];
            $position += 10;
        }

        return $list;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return static::t('Assign shopping group');
    }
}
