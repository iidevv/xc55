<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

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
        $this->relatedTargets['customer_profiles'][] = 'user_reward_points';

        parent::__construct($params);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['promotions'][self::ITEM_CHILDREN]['loyalty_program'] = [
            self::ITEM_TITLE      => static::t('Reward points'),
            self::ITEM_TARGET     => 'loyalty_program',
            self::ITEM_PERMISSION => 'manage orders',
            self::ITEM_WEIGHT     => 300,
        ];

        return $items;
    }
}
