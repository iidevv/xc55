<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core;

use XLite\Model\Cart;
use Qualiteam\SkinActGraphQLApi\Controller\Admin\GraphqlApi;

/**
 * Current session
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * [t-converted]
 */
class Session extends \XLite\Core\Session
{
    protected function useDumpSession(): bool
    {
        return parent::useDumpSession()
            || $this->isGraphqlRequest();
    }

    /**
     * @return bool
     */
    protected function isGraphqlRequest()
    {
        return \XLite::safeGetController()
            && \XLite::safeGetController() instanceof GraphqlApi;
    }

    /**
     * Restore cart from token
     *
     * @param string $token Cart token
     *
     * @return boolean
     */
    public static function restoreCartFromToken($token)
    {
        $cart = Cart::tryRetrieveCartByToken($token);

        if ($cart) {
            static::restoreCart($cart);

            return true;
        }

        return false;
    }

    /**
     * Restore cart from token and login user
     *
     * @param Cart $cart Cart
     *
     * @return boolean
     */
    protected static function restoreCart($cart)
    {
        // Check if cart needs to be restored
        $profile = $cart->getOrigProfile();

        if ($profile) {
            \XLite\Core\Auth::getInstance()->loginProfileById($profile->getProfileId());
            \XLite\Core\Auth::getInstance()->loginProfile($profile);
        }

        Cart::setObject($cart);

        return true;
    }

    /**
     * Generate random string
     *
     * @param integer $length Random string length OPTIONAL
     * @param boolean $alphanumeric Generate string from alphanumeric characters OPTIONAL
     *
     * @return string
     */
    public static function generateToken($length = 32, $alphanumeric = true)
    {
        if ($alphanumeric) {
            $symbols = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        } else {
            $symbols = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        }

        if ($length < 0) {
            $length = 32;
        }

        $count = strlen($symbols) - 1;
        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= $symbols[mt_rand(0, $count)];
        }

        return $str;
    }

    public function isAdminSessionExpired(): bool
    {
        if ($this->session->getMetadataBag() === null) {
            return true; // expired
        }

        return parent::isAdminSessionExpired();
    }
}
