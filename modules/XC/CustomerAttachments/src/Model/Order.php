<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Add item to order
     *
     * @param \XLite\Model\OrderItem $newItem Item to add
     *
     * @return boolean
     */
    public function addItem(\XLite\Model\OrderItem $newItem)
    {
        $result = parent::addItem($newItem);

        if ($result) {
            $attachments = $newItem->getCustomerAttachments();
            if (!empty($attachments)) {
                if (!$newItem->isPersistent()) {
                    \XLite\Core\Database::getEM()->flush();
                }

                foreach ($attachments as $attachment) {
                    $attachment->setOrderItem($newItem);
                }
            }
        }

        return $result;
    }
}
