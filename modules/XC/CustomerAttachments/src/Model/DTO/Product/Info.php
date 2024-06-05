<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        $this->default->is_customer_attachments_available = $object->getIsCustomerAttachmentsAvailable();
        $this->default->is_customer_attachments_required = $object->getIsCustomerAttachmentsRequired();
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->setIsCustomerAttachmentsAvailable((bool) $this->default->is_customer_attachments_available);
        $object->setIsCustomerAttachmentsRequired((bool) $this->default->is_customer_attachments_required);
    }
}
