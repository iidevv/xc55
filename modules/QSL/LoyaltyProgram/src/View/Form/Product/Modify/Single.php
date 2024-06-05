<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Form\Product\Modify;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Product Modify form.
 * @Extender\Mixin
 */
class Single extends \XLite\View\Form\Product\Modify\Single
{
    /**
     * Set validators pairs for products data.
     *
     * @param mixed &$data Data
     */
    protected function setDataValidators(&$data)
    {
        $data->addPair('autoRewardPoints', new \XLite\Core\Validator\Integer(), null, 'Auto reward points');
        $data->addPair('rewardPoints', new \XLite\Core\Validator\Integer(), null, 'Reward points');
    }
}
