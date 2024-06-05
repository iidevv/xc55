<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\Model\Repo;

use XCart\Extender\Mapping\Extender;
use QSL\OrderReports\Controller\Admin\OrderReports;
use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    /**
     * @param array $dateRange
     *
     * @return array
     */
    public function getSegmentByProduct($dateRange)
    {
        return $this->defineQueryGetSegmentByProduct($dateRange)
            ->setMaxResults(1000)
            ->getResult();
    }

    /**
     * @param array $dateRange
     *
     * @return AQueryBuilder
     */
    protected function defineQueryGetSegmentByProduct(array $dateRange)
    {
        return $this->createQueryBuilder('oi')
            ->select(
                'IDENTITY(oi.object) AS id',
                'SUM(oi.amount) AS amount',
                'SUM(oi.total) AS sales',
                "CONCAT(oi.name, ' (', oi.sku, ')') AS name"
            )
            ->linkInner('oi.order', 'o')
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', OrderReports::getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->andWhere('o INSTANCE OF \XLite\Model\Order')
            ->groupBy('oi.object')
            ->orderBy('sales', 'DESC');
    }

    /**
     * @param array $dateRange
     *
     * @return array
     */
    public function getSegmentByCategory($dateRange)
    {
        $return = $this->defineQueryGetSegmentByCategory($dateRange)->getResult();
        usort($return, static function (array $a, array $b) {
            if ($a['sales'] === $b['sales']) {
                return 0;
            }

            return ($a['sales'] < $b['sales']) ? 1 : -1;
        });

        return $return;
    }

    /**
     * @param array $dateRange
     *
     * @return AQueryBuilder
     */
    protected function defineQueryGetSegmentByCategory(array $dateRange)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\OrderItem')->createQueryBuilder('oi')
            ->linkInner('oi.order', 'o')
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->linkInner('oi.object', 'p')
            ->linkInner('p.categoryProducts', 'catProducts')
            ->linkInner('catProducts.category', 'category')
            ->select('category.category_id AS id')
            ->addSelect('SUM(oi.total) AS sales')
            ->addSelect('SUM(oi.amount) AS amount')
            ->linkInner('category.translations', 'ctranslations')
            ->addSelect('ctranslations.name as name')
            ->andWhere('ctranslations.code = :code')
            ->setParameter('code', \XLite\Core\Session::getInstance()->getLanguage()->getCode())
            ->setParameter('order_statuses', OrderReports::getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->andWhere('o INSTANCE OF \XLite\Model\Order')
            ->groupBy('category.category_id');
    }
}
