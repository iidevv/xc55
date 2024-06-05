<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\Model\OrderItem\Attachment;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order item attchament's storage
 *
 * @ORM\Entity
 * @ORM\Table (name="customer_attachments_storage")
 */
class Attachment extends \XLite\Model\Base\Storage
{
    /**
     * @var \XLite\Model\OrderItem
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\OrderItem", inversedBy="customerAttachments")
     * @ORM\JoinColumn (name="item_id", referencedColumnName="item_id")
     */
    protected $orderItem;

    /**
     * @param \XLite\Model\OrderItem $orderItem
     *
     * @return Attachment
     */
    public function setOrderItem(\XLite\Model\OrderItem $orderItem = null)
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    /**
     * @return \XLite\Model\OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }
}
