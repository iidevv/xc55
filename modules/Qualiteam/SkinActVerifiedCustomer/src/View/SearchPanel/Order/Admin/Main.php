<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\View\SearchPanel\Order\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Main admin orders list search panel
 * @Extender\Mixin
 */
class Main extends \XLite\View\SearchPanel\Order\Admin\Main
{
    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        return parent::defineHiddenConditions() + [
                'verificationStatus' => [
                    static::CONDITION_CLASS => '\Qualiteam\SkinActVerifiedCustomer\View\FormField\Select\VerificationStatus',
                    \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('SkinActVerifiedCustomer Customer verification status'),
                ],
            ];
    }
}
