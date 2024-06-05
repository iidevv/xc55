<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XLite\Core\Database;
use XLite\Model\Product;

class FreeGiftItem extends \XLite\Model\Repo\ARepo
{
    // {{{ Search

    public const SEARCH_GIFT_TIER_ID = 'giftTierId';

    /**
     * Get gift tier product list
     *
     * @param integer $giftTierId Gift Tier ID
     *
     * @return array(\Qualiteam\SkinActFreeGifts\Model\FreeGiftItem) Objects
     */
    public function getGiftTierProducts($giftTierId)
    {
        return $this->findByGiftTierId($giftTierId);
    }

    /**
     * Find by type
     *
     * @param integer $giftTierId Gift Tier ID
     *
     * @return array
     */
    protected function findByGiftTierId(int $giftTierId): array
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->{static::SEARCH_GIFT_TIER_ID} = $giftTierId;
        return $this->search($cnd);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param int                        $value Condition data
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareCndGiftTierId(QueryBuilder $qb, int $value): QueryBuilder
    {
        $f = $this->getMainAlias($qb);
        $qb = $qb->innerJoin($f . '.product', 'p')
            ->innerJoin($f . '.freeGift', 'gt')
            ->andWhere('gt.gift_tier_id = :giftTierId')
            ->setParameter('giftTierId', $value);

        return Database::getRepo(Product::class)->assignExternalEnabledCondition($qb);
    }
    // }}}

    protected function prepareCndCartSubtotal(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $f = $this->getMainAlias($queryBuilder);

        $queryBuilder
            ->innerJoin($f . '.freeGift', 'gt')
            ->andWhere('gt.tier_min_price <= :cart_subtotal AND :cart_subtotal <= gt.tier_max_price')
            ->setParameter('cart_subtotal', $value);
    }
}
