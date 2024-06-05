<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\API\Endpoint\SaleDiscount\Transformer;

use CDev\Sale\API\Endpoint\SaleDiscount\DTO\SaleDiscountOutput as OutputDTO;
use CDev\Sale\Module\CDev\Wholesale\API\Endpoint\SaleDiscount\DTO\SaleDiscountOutput as CurrentOutputDTO;
use CDev\Sale\API\Endpoint\SaleDiscount\Transformer\OutputTransformer as ParentOutputTransformer;
use CDev\Sale\Module\CDev\Wholesale\Model\SaleDiscount as CurrentModel;
use Exception;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Wholesale")
 */
class OutputTransformer extends ParentOutputTransformer
{
    /**
     * @param CurrentModel $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        /** @var CurrentOutputDTO $dto */
        $dto = parent::transform($object, $to, $context);

        $dto->apply_to_wholesale = $object->getApplyToWholesale();

        return $dto;
    }
}
