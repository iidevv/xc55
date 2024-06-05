<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomOrderStatuses\View\ItemsList\Model\Order\Status;

use Qualiteam\SkinActCustomOrderStatuses\View\FormField\Inline\Select\ActivePast;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Payment extends \XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\Payment
{
    protected function defineColumns()
    {
        return parent::defineColumns() + [
                'mobile_tab' => [
                    static::COLUMN_NAME    => static::t('SkinActCustomOrderStatuses mobile tab'),
                    static::COLUMN_CLASS   => ActivePast::class,
                    static::COLUMN_ORDERBY => 600,
                ],
            ];
    }
}