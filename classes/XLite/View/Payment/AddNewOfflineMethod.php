<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment;

use XCart\Extender\Mapping\ListChild;

/**
 * Add new offline payment method dialog widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AddNewOfflineMethod extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'add_new_offline_method';

        return $list;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'payment/add_method/parts/add_new_offline_method.twig';
    }
}
