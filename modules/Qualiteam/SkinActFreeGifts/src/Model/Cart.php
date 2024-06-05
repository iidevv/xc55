<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model;

use Qualiteam\SkinActFreeGifts\Model\FreeGift as FreeGiftModel;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Cart model
 *
 * @Extender\Mixin
 */
class Cart extends \XLite\Model\Cart
{
    /**
     * @return FreeGift|null
     */
    public function getFreeGiftTier($product = null): ?FreeGiftModel
    {
        $subtotal = $this->getFreeGiftsSubtotal();

        return Database::getRepo(FreeGiftModel::class)->getExactGiftTier($subtotal, $product);
    }

    public function hasGiftItemAlready(): bool
    {
        return (bool)Database::getRepo(\XLite\Model\OrderItem::class)->findOneBy([
            'order'     => $this,
            'freeGift'  => true,
        ]);
    }

    public function getFreeGiftsSubtotal(): float
    {
        return (float)Database::getRepo(\XLite\Model\OrderItem::class)
            ->createQueryBuilder('oi')
            ->select('SUM(oi.discountedSubtotal)')
            ->andWhere('oi.order = :cart')
            ->andWhere('oi.freeGift = :nonFreeGift')
            ->setParameter('cart', $this)
            ->setParameter('nonFreeGift', false)
            ->getSingleScalarResult();
    }
}
