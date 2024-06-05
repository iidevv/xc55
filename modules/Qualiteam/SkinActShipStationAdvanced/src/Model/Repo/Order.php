<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use Qualiteam\SkinActShipStationAdvanced\Main;
use Qualiteam\SkinActShipStationAdvanced\Model\ShipstationStatuses as ShipstationStatusesModel;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\After ("ShipStation\Api")
 */
class Order extends \XLite\Model\Repo\Order
{
    public function getOrdersFromRenewDate($dtOrderUpdateStart = '', $dtOrderUpdateEnd = '')
    {
        return $this->prepareOrdersFromRenewDate($dtOrderUpdateStart, $dtOrderUpdateEnd)->getResult();
    }

    protected function prepareOrdersFromRenewDate($dtOrderUpdateStart = '', $dtOrderUpdateEnd = '')
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->andWhere('o.lastRenewDate >= :lastRenewDateStart')
            ->andWhere('o.lastRenewDate <= :lastRenewDateEnd')
            ->setParameter('lastRenewDateStart', $dtOrderUpdateStart)
            ->setParameter('lastRenewDateEnd', $dtOrderUpdateEnd);

        $this->defineOrderItemsCondition($queryBuilder);
        $this->defineStatusesCondition($queryBuilder);

        return $queryBuilder;
    }

    protected function defineOrderItemsCondition(QueryBuilder $queryBuilder)
    {
        if (Main::isDeveloperMode()) {
            $qbSelect = $this->createQueryBuilder('ord')
                ->select('ord.order_id')
                ->linkInner('ord.items', 'ordiDev');
            $qbSelect->andWhere($qbSelect->expr()->notIn('ordiDev.sku', Main::getDeveloperModeProductSkus()));

            $dql = $qbSelect->getDQL();

            $queryBuilder
                ->linkInner('o.items', 'oiDev')
                ->addInCondition('oiDev.sku', Main::getDeveloperModeProductSkus());

            $queryBuilder->andWhere($queryBuilder->expr()->notIn('o.order_id', $dql));

            //echo $queryBuilder->getQuery()->getSQL();die;
        }
    }

    protected function defineStatusesCondition(QueryBuilder $queryBuilder)
    {
        $countStatuses = $this->getStatusesCount();

        if ($countStatuses > 0) {
            $statuses = $this->getStatuses();
            $cnd      = $queryBuilder->expr()->orX();

            for ($i = 0; $i < $countStatuses; $i++) {
                $cnd->add(
                    $queryBuilder->expr()->andX(
                        "o.paymentStatus = :paymentShipStationStatus_{$i}",
                        "o.shippingStatus = :shippingShipStationStatus_{$i}",
                    )
                );

                $queryBuilder->setParameter("paymentShipStationStatus_{$i}", $statuses[$i]->getPaymentStatus());
                $queryBuilder->setParameter("shippingShipStationStatus_{$i}", $statuses[$i]->getShippingStatus());
            }

            $queryBuilder->andWhere($cnd);
        }
    }

    protected function getStatusesCount(): int
    {
        return Database::getRepo(ShipstationStatusesModel::class)
            ->search(null, true);
    }

    protected function getStatuses(): mixed
    {
        return Database::getRepo(ShipstationStatusesModel::class)
            ->search();
    }
}
