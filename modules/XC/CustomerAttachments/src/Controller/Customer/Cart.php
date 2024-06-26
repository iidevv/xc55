<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * Prepare order item class for adding to cart.
     * This method takes \XLite\Model\Product class and amount and creates \XLite\Model\OrderItem.
     * This order item container will be added to cart in $this->addItem() method.
     *
     * @param \XLite\Model\Product $product Product class to add to cart OPTIOANL
     * @param integer              $amount  Amount of product to add to cart OPTIONAL
     *
     * @return \XLite\Model\OrderItem
     */
    protected function prepareOrderItem(\XLite\Model\Product $product = null, $amount = null)
    {
        $newItem = parent::prepareOrderItem($product, $amount);

        if (!is_null($newItem)) {
            $attachmentsIds = \XLite\Core\Request::getInstance()->attachments_ids;
            $allowedQuantity = \XC\CustomerAttachments\Core\CustomerAttachments::getAllowedQuantity();

            if (isset($attachmentsIds) && !empty($attachmentsIds)) {
                $attachmentsRepo = \XLite\Core\Database::getRepo('\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment');
                $processedQuantity = 0;
                $overAttachments = [];
                foreach ($attachmentsIds as $id) {
                    if ($processedQuantity < $allowedQuantity) {
                        $model = $attachmentsRepo->find($id);
                        $newItem->addCustomerAttachments($model);
                    } else {
                        $model = $attachmentsRepo->find($id);
                        $overAttachments[] = $model;
                    }
                    $processedQuantity++;
                }

                if (!empty($overAttachments)) {
                    $attachmentsRepo->deleteInBatch($overAttachments, true);
                }
            }
        }

        return $newItem;
    }
}
