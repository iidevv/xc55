<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\View\FormField\Select;


use XLite\Core\Database;

class OrderVerifiedStatusId extends \XLite\View\FormField\Select\Regular
{
    protected function getDefaultOptions()
    {
        /** @var \XLite\Model\Order\Status\Shipping[] $statuses */
        $statuses = Database::getRepo('\XLite\Model\Order\Status\Shipping')->findAll();

        $list = [
            0 => static::t('SkinActVerifiedCustomer Verified order fulfillment status not selected')
        ];

        if ($statuses) {

            foreach ($statuses as $status) {
                $list[$status->getId()] = $status->getName();
            }
        }

        return $list;
    }

}