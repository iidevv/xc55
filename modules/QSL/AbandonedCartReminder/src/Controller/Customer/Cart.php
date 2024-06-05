<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\AbandonedCartReminder\Core\TokenReplacer\AbandonmentEmail;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Controller\Customer\Checkout;
use XLite\Core\Converter;
use XLite\Core\TopMessage;
use XLite\Core\AuthToken;
use XLite\Core\Request;
use XLite\Core\Database;
use XLite\Core\Session;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    use ExecuteCachedTrait;

    /**
     * @return int|null
     */
    protected function getAbandonedCartId(): ?int
    {
        return intval($this->getRecoveryData()->{AbandonmentEmail::RECOVERY_LINK_PARAM_CART}) ?? null;
    }

    /**
     * @return int|null
     */
    protected function getRecoveryEmailId(): ?int
    {
        return intval($this->getRecoveryData()->{AbandonmentEmail::RECOVERY_LINK_PARAM_EMAIL}) ?? null;
    }

    /**
     * @return string|null
     */
    protected function getRecoveryToken(): ?string
    {
        return Request::getInstance()->{AbandonmentEmail::RECOVERY_LINK_PARAM_TOKEN};
    }

    /**
     * @return object|null
     */
    protected function getRecoveryData(): ?object
    {
        $token = $this->getRecoveryToken();

        return $this->executeCachedRuntime(static function () use ($token) {
            try {
                $data = AuthToken::decode($token);
            } catch (\Exception $e) {
            }

            return $data ?? null;
        }, [__CLASS__, __METHOD__, $token]);
    }

    /**
     * @throws \Exception
     */
    protected function doActionRecovery(): void
    {
        $recoveryData = $this->getRecoveryData();

        /** @var \QSL\AbandonedCartReminder\Model\Email $email */
        $email = ($emailId = $this->getRecoveryEmailId())
            ? Database::getRepo('QSL\AbandonedCartReminder\Model\Email')->find($emailId)
            : null;

        $abandonedCart = $email
            ? $email->getOrder()
            : Database::getRepo('XLite\Model\Cart')->find($this->getAbandonedCartId());


        $validLink = ($abandonedCart instanceof \XLite\Model\Cart)
            && $recoveryData
            && $abandonedCart
            && $abandonedCart->getProfile();

        $orderIdMatching   = $validLink && ($recoveryData->order_id === $abandonedCart->getOrderId());
        $profileIdMatching = $validLink && ($recoveryData->last_profile_id === $abandonedCart->getProfile()->getProfileId());

        if ($orderIdMatching && $profileIdMatching) {
            $outdatedAttr = false;
            /* @var \XLite\Model\OrderItem $item */
            foreach ($abandonedCart->getItems() as $item) {
                if (
                    $item->hasAttributeValues()
                    && !$item->isActualAttributes()
                ) {
                    $item->setAttributeValues([]);
                    $outdatedAttr = true;
                }
            }

            $abandonedCart->markCartAsRecovered($email);
            $abandonedCart->setLastVisitDate(Converter::time());
            $this->recoverAbandonedCart($abandonedCart);

            if ($outdatedAttr) {
                TopMessage::addWarning('Order item attributes are out-of-date');
            }
        } else {
            TopMessage::addError(
                static::t('The link is expired.')
            );
        }

        // Set return URL
        $this->setReturnURL($this->buildURL('cart'));
    }

    /**
     * @param \XLite\Model\Cart $abandonedCart
     *
     * @throws \Exception
     */
    protected function recoverAbandonedCart(\XLite\Model\Cart $abandonedCart): void
    {
        if (
            ($cart = $this->getCart())
            && is_null($cart->getProfile())
            && $cart->getOrderId() !== $abandonedCart->getOrderId()
        ) {
            Database::getEM()->remove($cart);
        }

        Auth::getInstance()->logoff();
        Auth::getInstance()->loginByToken($this->getRecoveryToken());
        \XLite\Model\Cart::setObject($abandonedCart);
        Session::getInstance()->{Checkout::CHECKOUT_AVAIL_FLAG} = time();
        $this->updateCart();
    }
}
