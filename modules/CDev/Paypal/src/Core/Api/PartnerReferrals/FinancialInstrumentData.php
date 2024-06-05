<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-financial_instrument_data
 *
 * @property \CDev\Paypal\Core\Api\PartnerReferrals\BankDetails[] bank_details
 */
class FinancialInstrumentData extends PayPalModel
{
    /**
     * @return \CDev\Paypal\Core\Api\PartnerReferrals\BankDetails[]
     */
    public function getBankDetails()
    {
        return $this->bank_details;
    }

    /**
     * @param \CDev\Paypal\Core\Api\PartnerReferrals\BankDetails[] $bank_details
     *
     * @return FinancialInstrumentData
     */
    public function setBankDetails($bank_details)
    {
        $this->bank_details = $bank_details;

        return $this;
    }

    /**
     * @param \CDev\Paypal\Core\Api\PartnerReferrals\BankDetails $bank_detail
     *
     * @return FinancialInstrumentData
     */
    public function addBankDetail($bank_detail)
    {
        if (!$this->getBankDetails()) {
            return $this->setBankDetails([$bank_detail]);
        }

        return $this->setBankDetails(
            array_merge($this->getBankDetails(), [$bank_detail])
        );
    }

    /**
     * @param \CDev\Paypal\Core\Api\PartnerReferrals\BankDetails $bank_detail
     *
     * @return FinancialInstrumentData
     */
    public function removeBankDetail($bank_detail)
    {
        return $this->setBankDetails(
            array_diff($this->getBankDetails(), [$bank_detail])
        );
    }
}
