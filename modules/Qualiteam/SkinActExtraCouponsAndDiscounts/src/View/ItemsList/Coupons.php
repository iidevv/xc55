<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\View\ItemsList;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Coupons extends \CDev\Coupons\View\ItemsList\Coupons
{
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return $column['code'] && $entity->getExtraCoupon()
            ? \XLite\Core\Converter::buildURL('extra_coupon', '', ['id' => $entity->getExtraCoupon()->getId()])
            : parent::buildEntityURL($entity, $column);
    }
}