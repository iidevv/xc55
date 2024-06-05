<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Logic\Order\Modifier;

/**
 * Discount for selected payment method.
 */
class SpecialOffers extends \XLite\Logic\Order\Modifier\Discount
{
    /**
     * Modifier code is the same as a base Discount - this will be aggregated to the single 'Discount' line in cart totals.
     */
    public const MODIFIER_CODE = 'SPECIAL_OFFER_DISCOUNT_CHEAPEST';

    /**
     * Modifier type (see \XLite\Model\Base\Surcharge)
     *
     * @var string
     */
    protected $type = \XLite\Model\Base\Surcharge::TYPE_DISCOUNT;

    /**
     * Modifier unique code.
     *
     * @var string
     */
    protected $code = self::MODIFIER_CODE;

    /**
     *
     * @var type Order breakdown into individual units.
     *
     * @var array
     */
    protected $orderUnits;


    /**
     * Check - can apply this modifier or not.
     *
     * @return boolean
     */
    public function canApply()
    {
        return parent::canApply();
    }

    /**
     * Calculate.
     *
     * @return void
     */
    public function calculate()
    {
        $this->resetOrderUnits();

        if ($this->canBeCalculatedNow()) {
            foreach ($this->getSpecialOffers() as $offer) {
                $processor = $offer->getOfferType()->getProcessor();
                if ($processor->canApplyOffer($offer, $this)) {
                    $processor->applyOffer($offer, $this);
                }
            }
        }
    }

    /**
     * @return bool
     */
    protected function canBeCalculatedNow()
    {
        return true;
    }

    /**
     * Get surcharge name.
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return \XLite\DataSet\Transport\Order\Surcharge
     */
    public function getSurchargeInfo(\XLite\Model\Base\Surcharge $surcharge)
    {
        $info = new \XLite\DataSet\Transport\Order\Surcharge();
        $info->name = $this->getSurchargeLabel();

        return $info;
    }

    /**
     * Returns the name of the surchage as it will appear on the cart page.
     *
     * @return string
     */
    protected function getSurchargeLabel()
    {
        return \XLite\Core\Translation::lbl('Special Offer discount');
    }


    /**
     * Searches and returns active special offers.
     *
     * @return array
     */
    protected function getSpecialOffers()
    {
        return $this->getSpecialOfferRepo()->findActiveOffers($this->getOrder()->getProfile());
    }

    /**
     * Returns the repository object for SpecialOffer model.
     *
     * @return \QSL\SpecialOffersBase\Model\Repo\SpecialOffer
     */
    protected function getSpecialOfferRepo()
    {
        return \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\SpecialOffer');
    }

    /**
     * Add order item surcharge
     *
     * @param \XLite\Model\OrderItem $item Order item
     * @param string $code Surcharge code
     * @param float $value Value
     * @param boolean $include Include flag OPTIONAL
     * @param boolean $available Availability flag OPTIONAL
     *
     * @return \XLite\Model\OrderItem\Surcharge
     */
    public function addItemSurcharge(
        \XLite\Model\OrderItem $item,
        $code,
        $value,
        $include = false,
        $available = true
    ) {
        return parent::addOrderItemSurcharge($item, $code, $value, $include, $available);
    }

    /**
     * Add order surcharge
     *
     * @param string $code Surcharge code
     * @param float $value Value
     * @param boolean $include Include flag OPTIONAL
     * @param boolean $available Availability flag OPTIONAL
     *
     * @return \XLite\Model\Order\Surcharge
     */
    public function addSurcharge($code, $value, $include = false, $available = true)
    {
        return parent::addOrderSurcharge($code, $value, $include, $available);
    }


    /**
     * Returns order items split into individual units.
     *
     * @return array
     */
    public function getOrderUnits()
    {
        if (!isset($this->orderUnits)) {
            $this->orderUnits = $this->defineOrderUnits();
        }

        return $this->orderUnits;
    }

    /**
     * Breaks order items into individual units.
     *
     * @return array
     */
    protected function defineOrderUnits()
    {
        $items = [];

        $n = 1;
        foreach ($this->getOrder()->getItems() as $lineItem) {
            $amount = $lineItem->getAmount();
            for ($i = 0; $i < $amount; $i++) {
                $items[] = new \QSL\SpecialOffersBase\Model\OrderUnit($n++, $lineItem);
            }
        }

        return $items;
    }

    /**
     * Drops the cached information about order units.
     *
     * @return void
     */
    protected function resetOrderUnits()
    {
        unset($this->orderUnits);
    }
}
