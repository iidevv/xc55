<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate order update
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Order history description pattern
     */
    public const TXT_CUSTOMER_ATTACHMENT_DESCRIPTION = 'Customer`s attachments were changed';

    /**
     * Changes history for customer attachments
     *
     * @var array
     */
    protected static $customerAttachmentsChanges = [];

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        $toDeleteAttachments = \XLite\Core\Request::getInstance()->delete_attachment;

        if (isset($toDeleteAttachments) && is_array($toDeleteAttachments)) {
            foreach ($toDeleteAttachments as $id => $value) {
                $attachmentModel = \XLite\Core\Database::getRepo('\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                    ->find($id);
                $productName = $attachmentModel->getOrderItem()->getName();
                $attachmentName = $attachmentModel->getFileName();

                \XLite\Core\Database::getRepo('\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                    ->deleteById($id, true);

                $msg = static::t('Attachment X is deleted', ['filename' => $attachmentName]);
                $this->setAttachmentsOrderChanges($productName, $msg);
            }

            \XLite\Core\OrderHistory::getInstance()->registerEvent(
                $this->getOrder()->getOrderId(),
                \XLite\Core\OrderHistory::CODE_ORDER_EDITED,
                static::t(static::TXT_CUSTOMER_ATTACHMENT_DESCRIPTION),
                [],
                serialize(static::$customerAttachmentsChanges)
            );
        }
    }

    /**
     * Set changes info about attachment
     *
     * @param $productName
     * @param $msg
     */
    protected function setAttachmentsOrderChanges($productName, $msg)
    {
        static::$customerAttachmentsChanges[$productName][] = [
            'old' => '',
            'new' => $msg,
        ];
    }
}
