<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model\Repo;

use XLite\Model\Product;

class FreeGift extends \XLite\Model\Repo\ARepo
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Get exact gift tier
     *
     * @param float $subtotal
     *
     * @return \Qualiteam\SkinActFreeGifts\Model\FreeGift|null
     */
    public function getExactGiftTier(float $subtotal, $product): ?\Qualiteam\SkinActFreeGifts\Model\FreeGift
    {
        if ($product) {
            return $this->createQueryBuilder()
                ->andWhere('f.tier_min_price <= :subtotal AND :subtotal <= f.tier_max_price')
                ->setParameter('subtotal', $subtotal)
                ->linkInner('f.items', 'item')
                ->linkInner('item.product', 'product')
                ->andWhere('product.product_id = :product_id')
                ->setParameter('product_id', $product->getProductId())
                ->getSingleResult();
        }

        return $this->createQueryBuilder()
            ->andWhere('f.tier_min_price <= :subtotal AND :subtotal <= f.tier_max_price')
            ->setParameter('subtotal', $subtotal)
            ->getSingleResult();

    }

    public function hasProduct(\Qualiteam\SkinActFreeGifts\Model\FreeGift $gift, Product $product): bool
    {
        return (bool)$this
            ->createQueryBuilder('fg')
            ->linkLeft('fg.items', 'item')
            ->linkLeft('item.product', 'product')
            ->andWhere('product.product_id = :product_id')
            ->andWhere('fg.gift_tier_id = :gift_tier_id')
            ->setParameter('gift_tier_id', $gift->getGiftTierId())
            ->setParameter('product_id', $product->getProductId())
            ->getSingleResult();
    }
}
