<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\View\ItemsList\Model\Order\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract admin order-based list
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\ItemsList\Model\Order\Admin\AAdmin
{
    /**
     * We add order number data attribute
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line entity OPTIONAL
     *
     * @return array
     */
    protected function getLineAttributes($index, \XLite\Model\AEntity $entity = null)
    {
        $result = parent::getLineAttributes($index, $entity);

        if ($entity) {
            $result['data-order-id'] = $entity->getOrderId();
        }

        return $result;
    }
}
