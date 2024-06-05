<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model;

/**
 * Individual order unit.
 *
 * Order item having qty=3 will split into 3 order units linked to the original order item.
 */
class OrderUnit extends \XLite\Base
{
    /**
     * Linked order item.
     *
     * @var \XLite\Model\OrderItem
     */
    protected $orderItem;

    /**
     * Identifier to distinct different units from the same line item.
     *
     * @var integer
     */
    protected $unitId;

    /**
     * Array of special offers applied on the unit.
     *
     * @var array
     */
    protected $appliedOffers = [];

    /**
     * Extra information about applied offers.
     *
     * The aray key is the offer identifier.
     *
     * @var array
     */
    protected $extraInfo = [];

    /**
     * Constructor.
     *
     * @param integer                $id        Unit identifier to distinct different units from the same line item.
     * @param \XLite\Model\OrderItem $orderItem Linked order item
     */
    public function __construct($id, \XLite\Model\OrderItem $orderItem)
    {
        parent::__construct();
        $this->unitId = $id;
        $this->orderItem = $orderItem;
    }

    /**
     * Returns the unit identifier.
     *
     * @return integer
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * Returns linked order item.
     *
     * @return \XLite\Model\OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * Returns name of the line item.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getOrderItem()->getName();
    }

    /**
     * Returns the line item product.
     *
     * @return string
     */
    public function getProduct()
    {
        return $this->getOrderItem()->getProduct();
    }

    /**
     * Returns identifier of the line item.
     *
     * @return integer
     */
    public function getOrderItemId()
    {
        return $this->getOrderItem()->getItemId();
    }

    /**
     * Returns the unit price calculated as the line item subtotal divided by the line item quantity.
     *
     * @return float
     */
    public function getUnitPrice()
    {
        $item = $this->getOrderItem();

        return $item->getSubtotal() / ($item->getAmount() ?: 1);
    }

    /**
     * Returns the net price for the line item.
     *
     * @return float
     */
    public function getItemNetPrice()
    {
        return $this->getOrderItem()->getItemNetPrice();
    }

    /**
     * Returns order.
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getOrderItem()->getOrder();
    }

    /**
     * Returns special offers in which this unit participates.
     *
     * @return array
     */
    public function getAppliedOffers()
    {
        return $this->appliedOffers;
    }

    /**
     * Marks the unit as participating in the offer.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $offer Special offer OPTIONAL
     *
     * @return void
     */
    public function markOfferAsApplied($offer, array $extraInfo = [])
    {
        $this->appliedOffers[] = $offer;
        $this->extraInfo[$offer->getOfferId()] = $extraInfo;
    }

    /**
     * Returns identifiers of special offers that this unit participates in.
     *
     * @return array
     */
    public function getAppliedOffersIds()
    {
        return array_keys($this->extraInfo);
    }

    /**
     * Returns extra information about the special offer that the item participates in.
     *
     * @param integer $offerId Offer identifier.
     *
     * @return array
     */
    public function getSpecialOfferExtraInfo($offerId)
    {
        return $this->extraInfo[$offerId] ?? null;
    }

    /**
     * Check if the special offer cannot be applied on the unit due to exclusions.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $offer Special offer.
     *
     * @return boolean
     */
    public function isExclusion(\QSL\SpecialOffersBase\Model\SpecialOffer $offer)
    {
        $result = false;

        // To be eligible, the unit must not participate in the same offer or in exclusion offers already
        $exclusions = $offer->getExclusions();
        $exclusions[] = $offer->getOfferId();

        foreach ($this->getAppliedOffersIds() as $id) {
            if (in_array($id, $exclusions)) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
