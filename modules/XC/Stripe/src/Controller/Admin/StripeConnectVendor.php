<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XC\Stripe\Main;

/**
 * @Extender\Depend ("XC\MultiVendor")
 */
class StripeConnectVendor extends \XC\MultiVendor\Controller\Admin\FinancialInfo
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        if ($this->getAction() === 'stripe_connect_return') {
            return parent::checkAccess()
                && (
                    !empty(\XLite\Core\Request::getInstance()->code)
                    || !empty(\XLite\Core\Request::getInstance()->error)
                ) && $this->checkStripeCode();
        }

        return parent::checkAccess();
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'stripe_connect_return';

        return $list;
    }

    /**
     * Check Stripe return code
     *
     * @return boolean
     */
    protected function checkStripeCode()
    {
        return \XLite\Core\Request::getInstance()->state == $this->generateUrlState();
    }

    /**
     * Get the Stripe Connect account
     *
     * @return void
     */
    protected function doActionConnectAccount()
    {
        $oauthUrl = $this->prepareOAuthLink();

        if ($oauthUrl) {
            \XLite\Core\Operator::redirect(
                $oauthUrl,
                302
            );
        } else {
            \XLite\Core\TopMessage::addError('Unable to create authorization link');

            $this->setReturnURL($this->buildFullURL('financialInfo'));
        }
    }

    protected function doActionDisconnectAccount()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        $stripeConnectMethod = Main::getStripeConnectMethod();

        if (
            $profile
            && $profile->isVendor()
            && $profile->getStripeSellerAccountId()
            && $stripeConnectMethod
            && $stripeConnectMethod->getProcessor()
        ) {
            $stripeConnectMethod->getProcessor()->deauthorizeVendorAccount($profile->getStripeSellerAccountId());

            $profile->setStripeSellerAccountId('');
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo('Stripe account has been successfully disconnected');
        }

        $this->setReturnURL($this->buildFullURL('financialInfo'));
    }

    protected function doActionStripeConnectReturn()
    {
        $result = false;
        $accountCode = \XLite\Core\Request::getInstance()->code ?? null;

        if (!$accountCode) {
            \XLite\Core\TopMessage::addError('Stripe Connect result code was not received');
        } else {
            $result = $this->saveConnectedAccount($accountCode);
        }

        if ($result === true) {
            \XLite\Core\TopMessage::addInfo('Stripe account ID have been saved');
        }

        $this->setReturnURL($this->buildFullURL('financialInfo'));
    }

    /**
     * @return string|null
     */
    protected function prepareOAuthLink()
    {
        $params = $this->prepareAuthorizeParams();

        $stripeConnectMethod = Main::getStripeConnectMethod();

        if (
            $params
            && $stripeConnectMethod
            && $stripeConnectMethod->getProcessor()
        ) {
            return $stripeConnectMethod->getProcessor()->getVendorOauthLink($params);
        }

        return null;
    }

    /**
     * @return array
     */
    protected function prepareAuthorizeParams()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        if ($profile && $profile->isVendor()) {
            return [
                'response_type'              => 'code',
                'redirect_uri'               => \XLite\Core\Converter::buildFullURL('stripe_connect_vendor', 'stripe_connect_return'),
                'scope'                      => 'read_write',
                'state'                      => $this->generateUrlState(),
                'stripe_user[email]'         => $profile ? $profile->getLogin() : '',
                'stripe_user[business_name]' => $profile->getVendorCompanyName(),
            ];
        }

        return [];
    }

    /**
     * @param string $accountCode
     *
     * @return bool
     */
    protected function saveConnectedAccount($accountCode)
    {
        $result = false;

        $stripeConnectMethod = Main::getStripeConnectMethod();

        if (
            $accountCode
            && $stripeConnectMethod
            && $stripeConnectMethod->getProcessor()
        ) {
            $params = [
                'code'          => $accountCode,
                'grant_type'    => 'authorization_code'
            ];

            $accountData = $stripeConnectMethod->getProcessor()->getConnectedAccountByCode($params);

            if ($stripeId = $accountData->stripe_user_id) {
                $result = $this->setSellerAccountId($stripeId);
            }
        }

        return $result;
    }

    /**
     * @param $accountId
     *
     * @return bool
     * @throws \Exception
     */
    protected function setSellerAccountId($accountId)
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        if ($profile && $profile->isVendor()) {
            $profile->setStripeSellerAccountId($accountId);

            \XLite\Core\Database::getEM()->flush();

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function generateUrlState()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        return hash_hmac(
            'sha512',
            \XLite\Core\Auth::getInstance()->getProfile()->getLogin(),
            $profile->getAdded()
        );
    }
}
