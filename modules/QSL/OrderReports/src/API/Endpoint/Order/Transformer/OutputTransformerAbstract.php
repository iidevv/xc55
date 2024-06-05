<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\API\Endpoint\Order\Transformer;

use Exception;
use QSL\OrderReports\API\Endpoint\Order\DTO\BaseOutput as DecoratedOutputDTO;
use QSL\OrderReports\Model\Order as DecoratedOrder;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput;
use XLite\Model\Order;

/**
 * @Extender\Mixin
 */
class OutputTransformerAbstract extends \XLite\API\Endpoint\Order\Transformer\OutputTransformerAbstract
{
    /**
     * @param Order|DecoratedOrder $object
     * @throws Exception
     */
    protected function basicTransform(BaseOutput $dto, $object, string $to, array $context = []): BaseOutput
    {
        /** @var DecoratedOutputDTO $output */
        $output = parent::basicTransform($dto, $object, $to, $context);

        $output->mobile_order = $object->getMobileOrder();

        return $output;
    }
}
