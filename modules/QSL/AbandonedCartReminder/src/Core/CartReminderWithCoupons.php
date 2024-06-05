<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core;

use XCart\Extender\Mapping\Extender;
use QSL\AbandonedCartReminder\Model\Reminder;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class CartReminderWithCoupons extends \QSL\AbandonedCartReminder\Core\CartReminder
{
    /**
     * Coupon generated for the reminder.
     *
     * @var \CDev\Coupons\Model\Coupon
     */
    protected $coupon;

    /**
     * Send the reminder.
     *
     * @return void
     */
    public function send()
    {
        $this->generateCoupons();

        return parent::send();
    }

    /**
     * Generate a coupon if needed.
     *
     * @return void
     */
    protected function generateCoupons()
    {
        $reminder = $this->getReminder();

        if ($reminder->requiresNewCoupon() && $this->isCouponAllowedForUser()) {
            $this->setCoupon(
                $this->generateCoupon(
                    $reminder->getNewCouponAmount(),
                    $reminder->getNewCouponType(),
                    $reminder->getNewCouponPeriod(),
                    $reminder->getNewCouponSingleUse()
                )
            );
        }
    }

    /**
     * Generate a new coupon.
     *
     * @param string  $amount Discount amount
     * @param string  $type   Discount type
     * @param integer $period Expiration period
     *
     * @return \CDev\Coupons\Model\Coupon
     */
    protected function generateCoupon($amount, $type, $period, $singleUse = false)
    {
        $coupon = new \CDev\Coupons\Model\Coupon();
        $coupon->markAsAbandonedCartCoupon();
        $coupon->setComment(static::t('Generated for an abandoned cart'));

        $coupon->setCode($this->getUniqueCouponCode());

        // Coupon can be used only once
        $coupon->setUsesLimit(1);

        // Set coupon value
        $coupon->setValue($amount);
        $coupon->setType($this->translateCouponType($type));

        // Set expiration date
        $today = \XLite\Core\Converter::time();
        $coupon->setDateRangeBegin($today);
        $coupon->setDateRangeEnd($today + $period * 24 * 60 * 60); // Convert days to timestamp seconds

        // Set "Coupon cannot be combined with other coupons" flag
        $coupon->setSingleUse($singleUse);

        // Link the coupon to the abandoned cart
        $coupon->setAbandonedCart($this->getCart());

        $coupon->create();

        $this->markUserAsReceivedCoupon($coupon);

        return $coupon;
    }

    /**
     * Translate the coupon type from the value returned by the Reminder model
     * to the value expected by the Coupon model.
     *
     * @param string $type Coupon type
     *
     * @return string
     */
    protected function translateCouponType($type)
    {
        return ($type == Reminder::COUPON_TYPE_PERCENT)
            ? \CDev\Coupons\Model\Coupon::TYPE_PERCENT
            : \CDev\Coupons\Model\Coupon::TYPE_ABSOLUTE;
    }

    /**
     * Return a unique code for a new coupon.
     *
     * @return string
     */
    protected function getUniqueCouponCode()
    {
        $repo = \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon');

        do {
            $code = \XLite\Core\Converter::generateRandomString(16, 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789');
        } while ($repo->findDuplicates($code));

        return $code;
    }

    /**
     * Returns tokens (placeholders) and the text that should replace the tokens.
     *
     * @return array
     */
    protected function processTokenReplacerParams($params)
    {
        if ($this->isCouponEnabled()) {
            $params['coupon'] = $this->getCoupon();
        }

        return $params;
    }

    /**
     * Returns the e-mail subject for the reminder.
     *
     * @return string
     */
    protected function getReminderSubject()
    {
        return $this->getReminder()->getReminderSubject($this->isCouponEnabled());
    }

    /**
     * Preprocesses the e-mail body for the reminder.
     *
     * @return string
     */
    protected function getReminderBody()
    {
        return $this->getReminder()->getReminderBody($this->isCouponEnabled());
    }

    /**
     * Check if generating a coupon is enabled for the reminder.
     *
     * @return bool
     */
    protected function isCouponEnabled()
    {
        return is_object($this->getCoupon());
    }

    /**
     * Check if the user is eligible for issuing a coupon for him/her.
     *
     * @return bool
     */
    protected function isCouponAllowedForUser()
    {
        $profile = $this->getCouponProfile();

        return !$profile
            || !\XLite\Core\Config::getInstance()->QSL->AbandonedCartReminder->abcr_one_coupon_per_user
            || ($profile->getNumberOfReminderCoupons() < 1);
    }

    /**
     * Mark the fact that the user has received a coupon for a reminder already.
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon model
     *
     * @return void
     */
    protected function markUserAsReceivedCoupon($coupon)
    {
        $profile = $this->getCouponProfile();

        if ($profile) {
            $profile->setNumberOfReminderCoupons(1 + $profile->getNumberOfReminderCoupons());
        }
    }

    /**
     * Returns the user that is going to received the reminder.
     *
     * @return \XLite\Model\Profile
     */
    protected function getCouponProfile()
    {
        return $this->executeCachedRuntime(function () {
            $cart = $this->getCart();
            $profile = $cart ? $cart->getProfile() : null;

            if ($profile) {
                $sameRegistered = \XLite\Core\Database::getRepo(\XLite\Model\Profile::class)->findByLogin($profile->getLogin());
                $profile = $sameRegistered ?: $profile;
            }

            return $profile;
        });
    }
}
