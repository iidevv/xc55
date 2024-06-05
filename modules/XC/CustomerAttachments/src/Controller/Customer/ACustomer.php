<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate ACustomer controller class
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Period of deleting requests
     */
    public const CUSTOMERS_PERIOD = 500;

    /**
     * Handles the request
     *
     * @return void
     */
    public function handleRequest()
    {
        parent::handleRequest();

        $rand = mt_rand(1, static::CUSTOMERS_PERIOD);
        if ($rand === 1) {
            $models = \XLite\Core\Database::getRepo('\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                ->findBy(['orderItem' => null]);

            if (!empty($models)) {
                \XLite\Core\Database::getRepo('\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                    ->deleteInBatch($models, true);
            }
        }
    }
}
