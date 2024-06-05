<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core\TokenReplacer;

use QSL\AbandonedCartReminder\Model\Email;
use XLite\Model\Cart;

/**
 * Provides methods to replace tokens in a reminder body.
 */
class AbandonmentEmail extends StoreInformation
{
    public const RECOVERY_LINK_PARAM_CART  = 'id';
    public const RECOVERY_LINK_PARAM_EMAIL = 'eid';
    public const RECOVERY_LINK_PARAM_TOKEN = 'token';
    public const RECOVERY_LINK_TTL         = 604800;

    /**
     * @var \XLite\Model\Cart
     */
    protected $cart = null;

    /**
     * @var \QSL\AbandonedCartReminder\Model\Email
     */
    protected $email;

    /**
     * @var \CDev\Coupons\Model\Coupon
     */
    protected $coupon = null;

    /**
     * Return list of allowed tokens.
     *
     * @return array
     */
    public static function getAllowedTokens(): array
    {
        return array_merge(
            parent::getAllowedTokens(),
            [
                'NAME',
                'CART_ITEMS',
                'RECOVERY_LINK',
                'COUPON_CODE',
                'COUPON_AMOUNT',
                'COUPON_EXPIRES',
            ]
        );
    }

    /**
     * Return replacement string for the NAME token.
     *
     * @return string
     */
    protected function getTokenStringName(): string
    {
        $cart = $this->getCart();
        $address = $cart ? $cart->getProfile()->getBillingAddress() : null;

        return $address ? (' ' . $address->getName()) : '';
    }

    /**
     * Return replacement string for the CART_ITEMS token.
     *
     * @return string
     */
    protected function getTokenStringCartItems(): string
    {
        return $this->renderProducts();
    }

    /**
     * Renders thw widget with a list of products in cart.
     *
     * @see \XLite\View\Mailer::compile()
     *
     * @return string
     */
    protected function renderProducts(): string
    {
        /** @var \XLite\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        return $layout->callInInterfaceZone(function () {
            $widget = new \QSL\AbandonedCartReminder\View\AbandonedCartItems();
            $widget->setWidgetParams(
                [
                    'cart' => $this->getCart(),
                ]
            );
            $widget->init();

            return $widget->getContent();
        }, \XLite::INTERFACE_MAIL, \XLite::ZONE_CUSTOMER);
    }

    /**
     * Return replacement string for the RECOVERY_LINK token.
     *
     * @return string
     */
    protected function getTokenStringRecoveryLink(): string
    {
        $cart  = $this->getCart();
        $email = $this->getEmail();
        $data  = [
            'order_id' => $cart->getOrderId(),
            'exp'      => \XLite\Core\Converter::time() + static::RECOVERY_LINK_TTL
        ];

        // There are the shipping address in the order, but not signed-in
        if ($profile = $cart->getProfile()) {
            $data['last_profile_id'] = $profile->getProfileId();
        }

        // Profile of the signed-in user
        $origProfile = $cart->getOrigProfile();
        if ($origProfile && !$origProfile->getAnonymous()) {
            $data['profile_id'] = $origProfile->getProfileId();
        }

        if ($email && $email->getEmailId()) {
            // If there is an email record for the notification, use the new parameter
            $data[static::RECOVERY_LINK_PARAM_EMAIL] = $email->getEmailId();
        } else {
            // Otherwise follow the old logic (without tracking individual email notifications)
            $data[static::RECOVERY_LINK_PARAM_CART] = $cart->getOrderId();
        }

        return \Includes\Utils\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL(
                'cart',
                'recovery',
                [static::RECOVERY_LINK_PARAM_TOKEN => \XLite\Core\AuthToken::generate($data)],
                \XLite::CART_SELF
            )
        );
    }

    /**
     * Return replacement string for the COUPON_CODE token.
     *
     * @return string
     */
    protected function getTokenStringCouponCode(): string
    {
        $coupon = $this->getCoupon();

        return $coupon ? $coupon->getCode() : '';
    }

    /**
     * Return replacement string for the COUPON_AMOUNT token.
     *
     * @return string
     */
    protected function getTokenStringCouponAmount(): string
    {
        $coupon = $this->getCoupon();
        $cart = $this->getCart();

        $string = '';

        if ($cart && $coupon) {
            $currency = $cart->getCurrency();
            $value = $coupon->getValue();
            $printValue = preg_replace('/[^\d]0+$/', '', round(doubleval($value), 2));
            $string = $coupon->isAbsolute()
                    ? \XLite\View\AView::formatPrice($value, $currency)
                    : ($printValue . '%');
        }

        return $string;
    }

    /**
     * Return replacement string for the COUPON_EXPIRES token.
     *
     * @return string
     */
    protected function getTokenStringCouponExpires(): string
    {
        $string = '';

        $coupon = $this->getCoupon();
        if ($coupon) {
            $expires = $coupon->getDateRangeEnd();
            $string = \XLite\Core\Converter::formatDate($expires);
        }

        return $string;
    }

    /**
     * Setter method for the Cart parameter.
     *
     * @param \XLite\Model\Cart $cart Cart model
     *
     * @return void
     */
    protected function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Getter method for the Cart parameter.
     *
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return $this->cart;
    }

    /**
     * Setter method for the Coupon parameter.
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon model OPTIONAL
     *
     * @return void
     */
    protected function setCoupon(\CDev\Coupons\Model\Coupon $coupon = null)
    {
        $this->coupon = $coupon;
    }

    /**
     * Getter method for the Coupon parameter.
     *
     * @return \CDev\Coupons\Model\Coupon
     */
    protected function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Setter method for the Email parameter.
     *
     * @param \QSL\AbandonedCartReminder\Model\Email $cart Cart model
     *
     * @return void
     */
    protected function setEmail(Email $model)
    {
        $this->email = $model;
    }

    /**
     * Getter method for the Email parameter.
     *
     * @return \QSL\AbandonedCartReminder\Model\Email
     */
    protected function getEmail()
    {
        return $this->email;
    }
}
