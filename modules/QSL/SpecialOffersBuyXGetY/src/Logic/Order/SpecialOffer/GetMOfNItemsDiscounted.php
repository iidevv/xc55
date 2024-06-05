<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\Logic\Order\SpecialOffer;

use XLite\InjectLoggerTrait;

/**
 * Class that implements the Get M of N items discounted/free logic.
 */
class GetMOfNItemsDiscounted extends \QSL\SpecialOffersBase\Logic\Order\SpecialOffer\ASpecialOffer
{
    use InjectLoggerTrait;

    /**
     * Checks if the offer has correct settings and can be applied on the order.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer                 $offer    Special offer model.
     * @param \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier Order modifier.
     *
     * @return boolean
     */
    public function canApplyOffer(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier
    ) {
        $itemsCount = $offer->getBxgyN();
        $rewardsCount = $offer->getBxgyM();

        return ($itemsCount > 0) && ($rewardsCount <= $itemsCount) && $this->checkCustomerMembership($offer, $modifier);
    }

    /**
     * Applies the special offer to the order being processed by the special offer modifier.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer                 $offer    Special offer model.
     * @param \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier Order modifier.
     *
     * @return void
     */
    public function applyOffer(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier
    ) {
        $itemsCount = $offer->getBxgyN();
        $rewardsCount = $offer->getBxgyM();
        $conditionsCount = $itemsCount - $rewardsCount;

        // Break line items into an array of individual product units
        $units = $modifier->getOrderUnits();

        // Order individual units by price
        usort($units, [get_class($this), 'compareItems']);

        $i = 0;
        $j = count($units) - 1;
        $group = 1;
        $conditions = [];
        $rewards = [];

        // Order currency
        $currency = $modifier->getOrder()->getCurrency();

        while ($i <= $j) {
            // Don't look among expensive products if we found the necessary number of them to trigger the
            // reward already.
            // We compare with ($n - $m) because $m cheapest products that receive the discount are among
            // those $n items that participate in the offer.
            if (count($conditions) < $conditionsCount) {
                // Skip products that participate in a special offer already
                if ($this->isUnitApplicableAsCondition($offer, $units[$i])) {
                    $conditions[] = $units[$i];
                }
                $i++;
            }

            // Don't look among cheap products if we found the necessary number of them to apply the reward already
            if (count($rewards) < $rewardsCount) {
                // Skip products that participate in a special offer already
                if ($this->isUnitApplicableAsReward($offer, $units[$j]) && ($i <= $j)) {
                    $rewards[] = $units[$j];
                }
                $j--;
            }

            if ((count($conditions) === $conditionsCount) && (count($rewards) === $rewardsCount)) {
                // We have found the products that can trigger the offer and the products that we may apply the offer on
                // Mark the products that have triggered the special offer
                foreach ($conditions as $unit) {
                    $unit->markOfferAsApplied(
                        $offer,
                        [
                            'role'  => 'trigger',
                            'group' => $group,
                        ]
                    );
                }
                $conditions = [];

                // Give the discount
                foreach ($rewards as $unit) {
                    // Calculate the discount sum
                    $unitPrice = $unit->getUnitPrice();
                    $discount = $this->calculateDiscount($offer, $unitPrice, $currency);
                    // The discount cannot be less than 0 and greater than the unit subtotal, the discounted subtotal or the line item total
                    $orderItem = $unit->getOrderItem();
                    $discount = max(0, min($unitPrice, $discount, $orderItem->getTotal(), $this->getItemDiscountedSubtotal($orderItem)));

                    // Ignore zero discounts
                    if ($discount > static::EPS) {
                        $unit->markOfferAsApplied(
                            $offer,
                            [
                                'role'     => 'reward',
                                'group'    => $group,
                                'discount' => $discount,
                            ]
                        );

                        if ($this->canAddSurcharge($orderItem, $modifier)) {
                            // Add the line item surcharge
                            $modifier->addItemSurcharge(
                                $orderItem,
                                $modifier->getCode(),
                                $discount * -1
                            );
                            // Update the discounted subtotal of the line item for proper calculations of discounted-subtotal-based surcharges later
                            $orderItem->setDiscountedSubtotal($orderItem->getDiscountedSubtotal() - $discount);
                            $orderItem->setTotal($orderItem->getTotal() - $discount);
                        }
                    }
                }
                $rewards = [];

                // Get the number of the next offer group
                $group++;
            }
        }

        if ($_ENV['APP_DEBUG']) {
            // Extra check to avoid long arrays being used as a function parameter
            $this->debugItemsBreakdown($offer, 'Order items breakdown after applying special offer #' . $offer->getOfferId() . ' (discount cheapest):', $units);
        }
    }

    /**
     * Whether the surcharge can be added.
     *
     * @param \XLite\Model\OrderItem                                    $orderItem
     * @param \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier
     *
     * @return bool
     */
    protected function canAddSurcharge(
        \XLite\Model\OrderItem $orderItem,
        \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier
    ): bool {
        return true;
    }

    protected function getItemDiscountedSubtotal(\XLite\Model\OrderItem $orderItem)
    {
        return $orderItem->getDiscountedSubtotalIncludingVAT();
    }

    /**
     * Check if the unit can be a condition for the special offer.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $offer Special offer model.
     * @param \QSL\SpecialOffersBase\Model\OrderUnit    $unit  Order unit
     *
     * @return boolean
     */
    protected function isUnitApplicableAsCondition(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        \QSL\SpecialOffersBase\Model\OrderUnit $unit
    ) {
        return !$unit->isExclusion($offer) && $this->checkConditionCategory($offer, $unit);
    }

    /**
     * Check if the unit can be a reward for the special offer.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $offer Special offer model.
     * @param \QSL\SpecialOffersBase\Model\OrderUnit    $unit  Order unit
     *
     * @return boolean
     */
    protected function isUnitApplicableAsReward(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        \QSL\SpecialOffersBase\Model\OrderUnit $unit
    ) {
        return !$unit->isExclusion($offer) && $this->checkConditionCategory($offer, $unit);
    }

    /**
     * Check if the unit can be a reward for the special offer.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $offer Special offer model.
     * @param \QSL\SpecialOffersBase\Model\OrderUnit    $unit  Order unit
     *
     * @return boolean
     */
    protected function checkConditionCategory(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        \QSL\SpecialOffersBase\Model\OrderUnit $unit
    ) {
        $eligible = !$offer->hasBxgyConditionCategories();
        if (!$eligible) {
            $product = $unit->getProduct();
            foreach ($product->getCategories() as $category) {
                $eligible = $offer->isBxgyConditionCategory($category);
                if ($eligible) {
                    break;
                }
            }
        }

        return $eligible;
    }

    /**
     * Calculates the discount sum.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer              $offer    Special offer model.
     * @param float                                                  $subtotal Sum that should be used as the base for calculating percent discounts.
     * @param \XLite\Model\Currency                                  $currency Currency used for the sum.
     *
     * @return float
     */
    protected function calculateDiscount(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        $subtotal,
        \XLite\Model\Currency $currency
    ) {
        $value = $offer->getBxgyDiscount();

        return ($offer->getBxgyDiscountType() === \QSL\SpecialOffersBase\Model\SpecialOffer::BXGY_DISCOUNT_TYPE_PERCENT) ? $currency->roundValue($subtotal * $value / 100) : $value;
    }

    /**
     * Compare items in the items breakdown.
     *
     * @param \QSL\SpecialOffersBase\Model\OrderUnit $item1 The first item to compare.
     * @param \QSL\SpecialOffersBase\Model\OrderUnit $item2 The second item to compare.
     *
     * @return integer
     */
    protected static function compareItems(
        \QSL\SpecialOffersBase\Model\OrderUnit $item1,
        \QSL\SpecialOffersBase\Model\OrderUnit $item2
    ) {

        $price1 = $item1->getUnitPrice();
        $price2 = $item2->getUnitPrice();

        return (abs($price2 - $price1) < static::EPS) ? 0 : (
                ($price1 > $price2) ? -1 : 1
                );
    }

    /**
     * Logs information about how special offer has been applied on the order.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer              $offer Special offer model.
     * @param string                                                 $title Log record title.
     * @param array                                                  $units Order units.
     *
     * @return void
     */
    protected function debugItemsBreakdown(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        $title,
        $units
    ) {
        $debug = [];

        $first = reset($units);
        $id = $offer->getOfferId();

        if ($first) {
            $user = $first->getOrder()->getProfile();
            $profile_id = $user ? $user->getProfileId() : null;
            $login = $user ? $user->getLogin() : 'guest customer';

            foreach ($units as $n => $unit) {
                $msg = '#' . $n . '. ' . $unit->getName() . ' (line item: ' . $unit->getOrderItemId() . '; ' . $unit->getItemNetPrice() . ' x ' . $unit->getOrderItem()->getAmount() . ' = ' . $unit->getOrderItem()->getSubtotal() . ')';
                $extra = $unit->getSpecialOfferExtraInfo($id);
                if (is_array($extra) && !empty($extra)) {
                    $discount = isset($extra['discount']) ? (', given discount: ' . $extra['discount']) : '';
                    $role = ($extra['role'] === 'trigger') ? $extra['role'] : ('trigger & ' . $extra['role']);
                    $msg .= ' (' . $role . ' for group #' . $extra['group'] . $discount . ')';
                }
                $debug[$n] = $msg;
            }

            $this->getLogger('QSL-SpecialOffersBuyXGetY')->debug($title, [
                'user' => "#{$profile_id}. {$login}",
                'debug' => $debug,
            ]);
        }
    }

    /**
     * Checks if the special offer is eligible for the customer's membership.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer                 $offer    Special offer model.
     * @param \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier Order modifier.
     *
     * @return boolean
     */
    protected function checkCustomerMembership(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier
    ) {
        $allowedMembershipIds = $offer->getBxgyConditionMemberships();

        $eligible = empty($allowedMembershipIds);
        if (!$eligible) {
            $profile = $modifier->getOrder()->getProfile();
            $membership = $profile ? $profile->getMembership() : null;
            $membershipId = $membership ? $membership->getMembershipId() : 0;
            $eligible = in_array($membershipId, $allowedMembershipIds);
        }

        return $eligible;
    }
}
