<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Order model
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\NotFinishedOrders")
 */
class CartWithNFO extends \XLite\Model\Cart
{
    /**
     * Performs some operations on cart before flushing it to the database. Use this as an extension point
     *
     * @param \XLite\Model\Cart $cart
     * @param boolean           $placeMode
     *
     * @return mixed
     */
    protected function postprocessCart($cart, $placeMode)
    {
        return parent::postprocessCart(
            $this->postprocessNFOClonedCartLoyaltyProgram($cart, $placeMode),
            $placeMode
        );
    }

    protected function postprocessNFOClonedCartLoyaltyProgram($cart, $placeMode)
    {
        // $cart is the new cart
        // $this is the existing cart that becomes "not finished"

        // The points might have been redeemed for the older cart that now
        // becomes "not finished" ($this). We should keep that flag to get
        // the points reverted back to the user in case of a failed payment.
        // But at the same time we should drop the flag for the cloned order,
        // otherwise the points will be returned twice.
        $cart->setPointsRedeemed(false);
        $cart->setPointsRewarded(false);

        return $cart;
    }

    /**
     * Retrieves order data from source
     *
     * @param \XLite\Model\Order $entity Source entity
     *
     * @return boolean
     */
    protected function insertDetailsFrom($entity)
    {
        parent::insertDetailsFrom($entity);

        $this->insertLoyaltyProgramDetailsFrom($entity);
    }

    /**
     * Retrieves order data from source
     *
     * @param \XLite\Model\Order $entity Source entity
     *
     * @return boolean
     */
    protected function insertLoyaltyProgramDetailsFrom($entity)
    {
        foreach ($entity->getRewardEvents() as $item) {
            $this->addRewardEvents($item);
            $item->setOrder($this);
            $entity->getRewardEvents()->removeElement($item);
        }
    }
}
