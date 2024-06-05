<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Order items
     *
     * @var   \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="CDev\Egoods\Model\OrderItem\PrivateAttachment", mappedBy="item", cascade={"all"})
     */
    protected $privateAttachments;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->privateAttachments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get available download attachments
     *
     * @param bool $availableOnly
     *
     * @return array
     */
    public function getDownloadAttachments($availableOnly = true)
    {
        $list = [];

        if (($this->isAllowDownloadAttachments() || !$availableOnly) && $this->getPrivateAttachments()) {
            foreach ($this->getPrivateAttachments() as $attachment) {
                if ($attachment->isAvailable() || !$availableOnly) {
                    $list[] = $attachment;
                }
            }
        }

        return $list;
    }

    /**
     * Create private attachments
     *
     * @return void
     */
    public function createPrivateAttachments()
    {
        // Remove old attachments
        foreach ($this->getPrivateAttachments() as $attachment) {
            \XLite\Core\Database::getEM()->remove($attachment);
        }
        $this->getPrivateAttachments()->clear();

        // Create attachments
        if ($this->getProduct() && $this->getProduct()->getId() && 0 < count($this->getProduct()->getAttachments())) {
            foreach ($this->getProduct()->getFilteredAttachments($this->getOrder()->getOrigProfile()) as $attachment) {
                if ($attachment->getPrivate()) {
                    $private = new \CDev\Egoods\Model\OrderItem\PrivateAttachment();
                    $this->addPrivateAttachments($private);
                    $private->setItem($this);
                    $private->setAttachment($attachment);
                    $private->setTitle($attachment->getPublicTitle());
                    $private->setBlocked(false);
                }
            }
        }
    }

    /**
     * Check attachments downloading availability
     *
     * @return boolean
     */
    protected function isAllowDownloadAttachments()
    {
        return in_array($this->getOrder()->getPaymentStatusCode(), [
               \XLite\Model\Order\Status\Payment::STATUS_PAID,
               \XLite\Model\Order\Status\Payment::STATUS_PART_PAID
           ])
           && !in_array($this->getOrder()->getShippingStatusCode(), [
                   \XLite\Model\Order\Status\Shipping::STATUS_WAITING_FOR_APPROVE,
                   \XLite\Model\Order\Status\Shipping::STATUS_WILL_NOT_DELIVER,
           ]);
    }

    /**
     * Add privateAttachments
     *
     * @param \CDev\Egoods\Model\OrderItem\PrivateAttachment $privateAttachments
     *
     * @return OrderItem
     */
    public function addPrivateAttachments(\CDev\Egoods\Model\OrderItem\PrivateAttachment $privateAttachments)
    {
        $this->privateAttachments[] = $privateAttachments;
        return $this;
    }

    /**
     * Get privateAttachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrivateAttachments()
    {
        return $this->privateAttachments;
    }
}
