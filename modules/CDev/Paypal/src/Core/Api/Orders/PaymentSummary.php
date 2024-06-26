<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-payment_summary
 *
 * @property \CDev\Paypal\Core\Api\Orders\Capture[] captures
 * @property \CDev\Paypal\Core\Api\Orders\Refund[]  refunds
 * @property \CDev\Paypal\Core\Api\Orders\Sale[]    sales
 * @property \CDev\Paypal\Core\Api\Orders\Sale[]    authorizations
 */
class PaymentSummary extends PayPalModel
{
    /**
     * @return \CDev\Paypal\Core\Api\Orders\Capture[]
     */
    public function getCaptures()
    {
        return $this->captures;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Capture[] $captures
     *
     * @return PaymentSummary
     */
    public function setCaptures($captures)
    {
        $this->captures = $captures;

        return $this;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Capture $capture
     *
     * @return PaymentSummary
     */
    public function addCapture($capture)
    {
        if (!$this->getCaptures()) {
            return $this->setCaptures([$capture]);
        }

        return $this->setCaptures(
            array_merge($this->getCaptures(), [$capture])
        );
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Capture $capture
     *
     * @return PaymentSummary
     */
    public function removeCapture($capture)
    {
        return $this->setCaptures(
            array_diff($this->getCaptures(), [$capture])
        );
    }

    /**
     * @return \CDev\Paypal\Core\Api\Orders\Refund[]
     */
    public function getRefunds()
    {
        return $this->refunds;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Refund[] $refunds
     *
     * @return PaymentSummary
     */
    public function setRefunds($refunds)
    {
        $this->refunds = $refunds;

        return $this;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Refund $refund
     *
     * @return PaymentSummary
     */
    public function addRefund($refund)
    {
        if (!$this->getRefunds()) {
            return $this->setRefunds([$refund]);
        }

        return $this->setRefunds(
            array_merge($this->getRefunds(), [$refund])
        );
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Refund $refund
     *
     * @return PaymentSummary
     */
    public function removeRefund($refund)
    {
        return $this->setRefunds(
            array_diff($this->getRefunds(), [$refund])
        );
    }

    /**
     * @return \CDev\Paypal\Core\Api\Orders\Sale[]
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Sale[] $sales
     *
     * @return PaymentSummary
     */
    public function setSales($sales)
    {
        $this->sales = $sales;

        return $this;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Sale $sale
     *
     * @return PaymentSummary
     */
    public function addSale($sale)
    {
        if (!$this->getSales()) {
            return $this->setSales([$sale]);
        }

        return $this->setSales(
            array_merge($this->getSales(), [$sale])
        );
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Sale $sale
     *
     * @return PaymentSummary
     */
    public function removeSale($sale)
    {
        return $this->setSales(
            array_diff($this->getSales(), [$sale])
        );
    }

    /**
     * @return \CDev\Paypal\Core\Api\Orders\Sale[]
     */
    public function getAuthorizations()
    {
        return $this->authorizations;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Sale[] $authorizations
     *
     * @return PaymentSummary
     */
    public function setAuthorizations($authorizations)
    {
        $this->authorizations = $authorizations;

        return $this;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Sale $authorization
     *
     * @return PaymentSummary
     */
    public function addAuthorization($authorization)
    {
        if (!$this->getAuthorizations()) {
            return $this->setAuthorizations([$authorization]);
        }

        return $this->setAuthorizations(
            array_merge($this->getAuthorizations(), [$authorization])
        );
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Sale $authorization
     *
     * @return PaymentSummary
     */
    public function removeAuthorization($authorization)
    {
        return $this->setAuthorizations(
            array_diff($this->getAuthorizations(), [$authorization])
        );
    }
}
