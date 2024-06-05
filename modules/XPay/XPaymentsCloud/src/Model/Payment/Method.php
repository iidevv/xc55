<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Payment;

use XCart\Extender\Mapping\Extender;
use \XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Payment method
 *
 * @Extender\Mixin
 */
abstract class Method extends \XLite\Model\Payment\Method implements \XLite\Base\IDecorator
{
    /**
    * Get method_id
    *
    * @return integer
    */
    public function getMethodId()
    {
        return (
            $this->isLegacyXpaymentsMethod()
            && XPaymentsHelper::getPaymentMethod()
            && $this->getFromMarketplace()
        )
            ? XPaymentsHelper::getPaymentMethod()->getMethodId()
            : parent::getMethodId();
    }

    /**
     * Get added
     *
     * @return bool
     */
    public function getAdded()
    {
        $result = parent::getAdded();

        if ($this->isLegacyXpaymentsMethod()) {
            if (XPaymentsHelper::getPaymentMethod()->getAdded()) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Call custom handler when adding/removing payment method
     *
     * @param boolean $added Property value
     *
     * @return \XLite\Model\Payment\Method
     */
    public function setAdded($added)
    {
        $result = parent::setAdded($added);

        if (XPaymentsHelper::XPAYMENTS_SERVICE_NAME == $this->getServiceName()) {
            if ($added) {
                XPaymentsHelper::onAddPaymentMethod($this);
            } else {
                XPaymentsHelper::onRemovePaymentMethod($this);
            }
        }

        return $result;
    }

    /**
     * Returns true if it is an old X-Payments payment method
     *
     * @return bool
     */
    public function isLegacyXpaymentsMethod()
    {
        return false !== strpos($this->getServiceName(), 'XPayments.Allowed')
            || false !== strpos($this->getServiceName(), 'SavedCard');
    }

    /**
     * Get warning note
     *
     * @return string
     */
    public function getWarningNote()
    {
        if (
            $this->isXpaymentsWallet()
            && $this->getProcessor()
        ) {
            $message = $this->getProcessor()->getWarningNote($this);
        }

        if (empty($message)) {
            $message = parent::getWarningNote();
        }

        return $message;
    }

    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        if (
            $this->isXpaymentsWallet()
            && $this->getProcessor()
        ) {
            $message = $this->getWarningNote();
        } else {
            $message = parent::getNotSwitchableReason();
        }

        return $message;
    }

    /**
     * Returns true if it is X-Payments or wallet payment method
     *
     * @return bool
     */
    public function isXpayments()
    {
        return in_array(
            $this->getServiceName(),
            XPaymentsHelper::getServiceNames()
        );
    }

    /**
     * Returns true if it is Apple Pay payment method
     *
     * @return bool
     */
    public function isXpaymentsApplePay()
    {
        return (XPaymentsHelper::APPLE_PAY_SERVICE_NAME == $this->getServiceName());
    }

    /**
     * Returns true if it is X-Payments Apple Pay/Google Pay/etc payment method
     *
     * @return bool
     */
    public function isXpaymentsWallet()
    {
        return in_array(
            $this->getServiceName(),
            XPaymentsHelper::getWalletServiceNames()
        );
    }

    /**
     * Returns walletId if it is an Apple Pay/Google Pay/etc or empty string if not
     *
     * @return string
     */
    public function getXpaymentsWalletId()
    {
        return XPaymentsHelper::getMethodWalletId($this);
    }

}
