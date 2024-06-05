<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class OrderReportsCoupons extends \QSL\OrderReports\Controller\Admin\OrderReports
{
    protected function getSchemas()
    {
        $list = parent::getSchemas();

        $list['coupon'] = [
            'param_name'    => 'Coupon',
            'quantity_name' => 'Orders placed',
            'url'           => '',
        ];

        return $list;
    }

    protected function getQueryBuilderByRepo(string $entity)
    {
        return \XLite\Core\Database::getRepo($entity)->createQueryBuilder('o');
    }

    protected function getSegmentByCoupon()
    {
        $dateRange = $this->getDateRangeValue();

        $dbResult = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->select('SUM(o.total) AS sales')
            ->linkLeft('o.usedCoupons', 'coupon')
            ->addSelect('COUNT(o) AS amount', 'coupon.code AS name')
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->groupBy('name')
            ->orderBy('sales', 'DESC')
            ->getArrayResult();

        $return = [];

        foreach ($dbResult as $result) {
            $return[] = [
                'name'   => $this->generateCouponNameForReports($result['name']),
                'sales'  => $result['sales'],
                'amount' => $result['amount'],
            ];
        }

        return $return;
    }

    protected function generateCouponNameForReports($coupon)
    {
        if (empty($coupon)) {
            return static::t('No coupon');
        }

        $c = \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon')->findOneByCode($coupon);

        if (!is_null($c)) {
            $value = $c->value;
            $type  = $c->type;
        } else {
            $type = '';
        }

        $return = $coupon;

        $shippingCouponType = defined('\\CDev\\Coupons\\Model\\Coupon::TYPE_FREESHIP')
            ? \CDev\Coupons\Model\Coupon::TYPE_FREESHIP
            : 'S'; // must be so;

        if ($type == \CDev\Coupons\Model\Coupon::TYPE_PERCENT) {
            $return .= ' (' . $value . '% ' . static::t('(off)') . ')';
        } elseif ($type == \CDev\Coupons\Model\Coupon::TYPE_ABSOLUTE) {
            $return .= ' (' . \XLite\View\AView::formatPrice($value) . ' ' . static::t('(off)') . ')';
        } elseif ($type == $shippingCouponType) {
            $return .= ' (' . static::t('Free shipping') . ')';
        } elseif (empty($type)) {
            $return .= ' (' . static::t('Coupon has been deleted') . ')';
        }

        return $return;
    }
}
