<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-credential
 *
 * @property \CDev\Paypal\Core\Api\PartnerReferrals\Signature   signature
 * @property \CDev\Paypal\Core\Api\PartnerReferrals\Certificate certificate
 */
class Credential extends PayPalModel
{
    /**
     * @return \CDev\Paypal\Core\Api\PartnerReferrals\Signature
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param \CDev\Paypal\Core\Api\PartnerReferrals\Signature $signature
     *
     * @return Credential
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return \CDev\Paypal\Core\Api\PartnerReferrals\Certificate
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param \CDev\Paypal\Core\Api\PartnerReferrals\Certificate $certificate
     *
     * @return Credential
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;

        return $this;
    }
}
