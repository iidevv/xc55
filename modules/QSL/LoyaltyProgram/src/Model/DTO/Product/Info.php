<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Model\DTO\Base\CommonCell;

/**
 * Product
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $box = $this->prices_and_inventory->reward_points_box;
        $object->setAutoRewardPoints((bool) $box->auto_reward_points);
        $object->setRewardPoints(intval($box->reward_points));

        parent::populateTo($object, $rawData);
    }

    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        $schema                                        = [
            'auto_reward_points' => $object->getAutoRewardPoints(),
            'reward_points'      => $object->getRewardPoints(),
        ];
        $this->prices_and_inventory->reward_points_box = new CommonCell($schema);
    }
}
