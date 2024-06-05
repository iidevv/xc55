<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Controller\Customer;

use CDev\GoogleAnalytics\Core\GA;
use XCart\Extender\Mapping\Extender;
use CDev\GoogleAnalytics\Model\Profile;

/**
 * Class ACustomer
 *
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * @param array $old
     * @param array $new
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getCartFingerprintDifference(array $old, array $new)
    {
        $result = parent::getCartFingerprintDifference($old, $new);

        $cellKeys = [
            'shippingMethodName',
            'paymentMethodName',
        ];

        foreach ($cellKeys as $name) {
            $old[$name] = $old[$name] ?? '';
            $new[$name] = $new[$name] ?? '';

            if ($old[$name] !== $new[$name]) {
                $result[$name] = $new[$name];
            }
        }

        return $result;
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    protected function updateCart($silent = false)
    {
        parent::updateCart($silent);

        /** @var Profile $profile */
        $profile = $this->getCart()->getProfile();

        if ($profile) {
            if ($cid = $this->parseClientIdCookie()) {
                $profile->setGaClientId($cid);
            }

            if ($sessionId = $this->getGaSessionId()) {
                $profile->setGaSessionId($sessionId);
            }

            $profile->update();
        }
    }

    protected function parseClientIdCookie(): ?string
    {
        $cid = null;

        if (isset($_COOKIE['_ga'])) {
            $gaParts = explode('.', $_COOKIE["_ga"], 4);
            $cid1 = $gaParts[2] ?? '';
            $cid2 = $gaParts[3] ?? '';
            $contents = [
                'cid'         => $cid1 . '.' . $cid2,
            ];

            $cid = $contents['cid'];
        }

        return $cid;
    }

    protected function getGaSessionId(): ?string
    {
        return $this->parseXcartGaSessionCookie() ?: $this->parseGaSessionCookie();
    }

    protected function parseXcartGaSessionCookie(): ?string
    {
        return $_COOKIE['xcart_ga_session'] ?? null;
    }

    protected function parseGaSessionCookie(): ?string
    {
        $sessionId = null;

        $gaCookieName = $this->getGaCookieName();
        if ($cookieValue = $_COOKIE[$gaCookieName] ?? null) {
            $gaParts = explode('.', $cookieValue, 4);

            $sessionId = $gaParts[2] ?? null;
        }

        return $sessionId;
    }

    protected function getGaCookieName(): string
    {
        $measurementId = GA::getResource()->getMeasurementId();

        return '_ga_' . str_replace('G-', '', $measurementId);
    }
}
