<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XC\AvaTax\Core\TaxCore;
use XC\AvaTax\Logic\Order\Modifier\StateTax;

/**
 * Order
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * AvaTax errors flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $avaTaxErrorsFlag = false;

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        parent::processSucceed();

        if ($this->getAvaTaxErrorsFlag()) {
            $cacheDriver = \XLite\Core\Database::getCacheDriver();
            $cacheId     = \XLite\Core\Session::getInstance()->getID();
            $messages    = $cacheDriver->fetch('avatax_last_errors' . $cacheId);
            \XLite\Core\OrderHistory::getInstance()->registerEvent(
                $this->getOrderId(),
                'AVATAX_HAS_NOT_TAXES',
                'The order was created with tax value not calculated',
                [],
                $messages ? implode('; ', $messages) : ''
            );
        } elseif ($this->isAvataxTransactionsApplicable()) {
            TaxCore::getInstance()->setFinalCalculationFlag(true);
            TaxCore::getInstance()->getStateTax($this);
        }
    }

    /**
     * Prepare order before remove operation
     */
    public function prepareBeforeRemove()
    {
        parent::prepareBeforeRemove();

        if ($this->isAvataxTransactionsApplicable() && $this->getOrderNumber()) {
            TaxCore::getInstance()->voidTransactionRequest($this, TaxCore::DOC_DELETED);
        }
    }

    /**
     * A "change status" handler for avataxVoidTransaction, is set in \XC\AvaTax\Model\Order\Status\Payment
     */
    public function processAvataxVoidTransaction()
    {
        if ($this->isAvataxTransactionsApplicable()) {
            TaxCore::getInstance()->voidTransactionRequest($this);
        }
    }

    /**
     * A "change status" handler for avataxAdjustTransaction, is set in \XC\AvaTax\Model\Order\Status\Payment
     */
    public function processAvataxAdjustTransaction()
    {
        if ($this->isAvataxTransactionsApplicable()) {
            TaxCore::getInstance()->adjustTransactionRequest($this, TaxCore::PRICE_ADJUSTED);
        }
    }

    /**
     * Set avaTaxErrorsFlag
     *
     * @param boolean $avaTaxErrorsFlag
     * @return Order
     */
    public function setAvaTaxErrorsFlag($avaTaxErrorsFlag)
    {
        $this->avaTaxErrorsFlag = $avaTaxErrorsFlag;

        return $this;
    }

    /**
     * Get avaTaxErrorsFlag
     *
     * @return boolean
     */
    public function getAvaTaxErrorsFlag()
    {
        return $this->avaTaxErrorsFlag;
    }

    public function hasAvataxTaxes(): bool
    {
        $result     = false;
        $surcharges = $this->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_TAX);
        foreach ($surcharges as $surcharge) {
            /** @var \XLite\Model\Order\Surcharge $surcharge */
            if ($this->isAvataxSurcharge($surcharge)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    protected function isAvataxTransactionsApplicable(): bool
    {
        return TaxCore::getInstance()->isValid()
            && $this->hasAvataxTaxes();
    }

    /**
     * Return true if code is Avatax surcharge code
     *
     * @param \XLite\Model\Order\Surcharge $surcharge Surcharge
     */
    protected function isAvataxSurcharge(\XLite\Model\Order\Surcharge $surcharge): bool
    {
        $modifier = $surcharge->getOwner()->getModifier(
            \XLite\Model\Base\Surcharge::TYPE_TAX,
            StateTax::MODIFIER_CODE
        );

        /** @var \XLite\Logic\Order\Modifier\AModifier $modifier */
        return $modifier->isSurchargeOwner($surcharge);
    }
}
