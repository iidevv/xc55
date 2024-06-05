<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Order review key
     *
     * @var \XC\Reviews\Model\OrderReviewKey
     *
     * @ORM\OneToOne (targetEntity="XC\Reviews\Model\OrderReviewKey", mappedBy="order", cascade={"all"}, fetch="LAZY")
     */
    protected $reviewKey;

    /**
     * Process action 'Shipping status changed to Delivered'
     *
     * @return void
     */
    protected function processReviewKey()
    {
        $this->createReviewKey();
    }

    /**
     * Create review key
     *
     * @return void
     */
    public function createReviewKey()
    {
        if (
            \XC\Reviews\Main::isCustomerFollowupEnabled()
            && $this->isOrderValidForReviewKey()
        ) {
            $reviewKey = new \XC\Reviews\Model\OrderReviewKey();
            $reviewKey->setOrder($this);
            $reviewKey->setAddedDate(\XLite\Core\Converter::time());
            $reviewKey->setKeyValue(md5(sprintf('%d:%d', $this->getOrderId(), microtime(true))));
            $this->setReviewKey($reviewKey);

            \XLite\Core\Database::getEM()->persist($reviewKey);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Return true if this order is valid for create review key
     *
     * @return boolean
     */
    protected function isOrderValidForReviewKey()
    {
        return !$this->getReviewKey()
            && $this->getPaymentStatusCode() === \XLite\Model\Order\Status\Payment::STATUS_PAID
            && (
                $this->getShippingStatusCode() === \XLite\Model\Order\Status\Shipping::STATUS_DELIVERED
                || !$this->isShippable()
            );
    }

    // {{{ Default getters and setters

    /**
     * Get reviewKey
     *
     * @return \XC\Reviews\Model\OrderReviewKey
     */
    public function getReviewKey()
    {
        return $this->reviewKey;
    }

    /**
     * Set reviewKey
     *
     * @param \XC\Reviews\Model\OrderReviewKey $value
     * @return $this
     */
    public function setReviewKey($value)
    {
        $this->reviewKey = $value;
        return $this;
    }

    /**
     * @param string $type Type
     *
     * @return array
     */
    protected function getStatusHandlersForCast($type)
    {
        $class = '\XC\Reviews\Model\Order\Status\\' . ucfirst($type);

        return array_merge_recursive(parent::getStatusHandlersForCast($type), $class::getModuleStatusHandlers());
    }
    // }}}
}
