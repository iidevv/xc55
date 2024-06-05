<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\FormField\Select;


use XLite\Core\Database;

class ProductToAddSelect extends \XLite\View\FormField\Select\Regular
{
    protected function getDefaultOptions()
    {
        $qb = Database::getRepo('XLite\Model\Product')->createQueryBuilder('p');

        $qb->where('p.enabled = :enabledPaidMembershipProduct')
            ->andWhere('p.appointmentMembership IS NOT NULL')
            ->setParameter('enabledPaidMembershipProduct', true);

        /** @var \XLite\Model\Product[] $products */
        $products = $qb->getResult();

        $list = [
            0 => static::t('SkinActProMembership paid membership not selected')
        ];

        $map = [
            \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_DAY => static::t('Day'),
            \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_WEEK => static::t('Week'),
            \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_MONTH => static::t('Month'),
            \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_YEAR => static::t('Year'),
        ];

        if ($products) {

            foreach ($products as $product) {

                $membership = $product->getAppointmentMembership();
                $type = $map[$product->getAssignedMembershipTTLType()];
                $ttl = $product->getAssignedMembershipTTL();
                $list[$product->getProductId()] =
                    $membership->getName() . ' / ' . $ttl . ' ' . $type . ' / '.static::formatPrice($product->getPrice());
            }
        }

        return $list;
    }

}