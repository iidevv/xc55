<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Decorate order item model to add customer attachments
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Customer file attachments
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment", mappedBy="orderItem", cascade={"all"})
     */
    protected $customerAttachments;

    /**
     * Is customer attachment available
     *
     * @return boolean
     */
    public function isCustomerAttachable()
    {
        return $this->getObject()->getIsCustomerAttachmentsAvailable();
    }

    /**
     * Is customer attachment required
     *
     * @return boolean
     */
    public function isCustomerAttachmentsRequired()
    {
        return $this->getObject()->isCustomerAttachmentsMandatory();
    }

    /**
     * Get customer attachments of this order item
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|void
     */
    public function getCustomerAttachments()
    {
        return $this->customerAttachments ? $this->customerAttachments : null;
    }

    /**
     * This key is used when checking if item is unique in the cart
     *
     * @return string
     */
    public function getKey()
    {
        $result = parent::getKey();

        if ($this->getCustomerAttachments()) {
            foreach ($this->getCustomerAttachments() as $attachment) {
                $result .= '||' . $attachment->getPath();
            }
        }

        return $result;
    }

    /**
     * Check if item is allowed to add to cart
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $result = parent::isConfigured();

        if (
            $result
            && $this->getObject()
        ) {
            $isAllowed = $this->getObject()->getIsCustomerAttachmentsAvailable();
            $isRequired = $this->getObject()->isCustomerAttachmentsMandatory();
            $attachmentsCount = $this->getCustomerAttachments() ? count($this->getCustomerAttachments()) : 0;

            $result = !$isAllowed || !$isRequired || $attachmentsCount;

            if ($isRequired && !$attachmentsCount) {
                \XLite\Core\TopMessage::addError('You have to attach file to product');
            }
        }

        return $result;
    }

    /**
     * Add customerAttachments
     *
     * @param \XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment $customerAttachments
     * @return OrderItem
     */
    public function addCustomerAttachments(\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment $customerAttachments)
    {
        $this->customerAttachments[] = $customerAttachments;
        return $this;
    }
}
