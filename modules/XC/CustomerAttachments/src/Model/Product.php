<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Decorate product model
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Product is available for customer attachments
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $isCustomerAttachmentsAvailable = false;

    /**
     * Attachment is required for add to cart
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $isCustomerAttachmentsRequired = false;

    /**
     * Set isCustomerAttachmentsAvailable
     *
     * @param boolean $isCustomerAttachmentsAvailable
     * @return Product
     */
    public function setIsCustomerAttachmentsAvailable($isCustomerAttachmentsAvailable)
    {
        $this->isCustomerAttachmentsAvailable = $isCustomerAttachmentsAvailable;
        return $this;
    }

    /**
     * Get isCustomerAttachmentsAvailable
     *
     * @return boolean
     */
    public function getIsCustomerAttachmentsAvailable()
    {
        return $this->isCustomerAttachmentsAvailable;
    }

    /**
     * Set isCustomerAttachmentsRequired
     *
     * @param boolean $isCustomerAttachmentsRequired
     * @return Product
     */
    public function setIsCustomerAttachmentsRequired($isCustomerAttachmentsRequired)
    {
        $this->isCustomerAttachmentsRequired = $isCustomerAttachmentsRequired;
        return $this;
    }

    /**
     * Get isCustomerAttachmentsRequired
     *
     * @return boolean
     */
    public function getIsCustomerAttachmentsRequired()
    {
        return $this->isCustomerAttachmentsRequired;
    }

    /**
     * Check if attachment is mandatory for this product
     *
     * @return boolean
     */
    public function isCustomerAttachmentsMandatory()
    {
        return $this->getIsCustomerAttachmentsAvailable() && $this->getIsCustomerAttachmentsRequired();
    }
}
