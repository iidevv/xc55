<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\API\Endpoint\SaleDiscount\Transformer;

use CDev\Sale\API\Endpoint\SaleDiscount\DTO\SaleDiscountOutput as OutputDTO;
use CDev\GoSocial\API\Endpoint\SaleDiscount\DTO\SaleDiscountOutput as CurrentOutputDTO;
use CDev\GoSocial\Model\SaleDiscount as CurrentModel;
use CDev\Sale\API\Endpoint\SaleDiscount\Transformer\OutputTransformer as ParentOutputTransformerAlias;
use Exception;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Sale")
 */
class OutputTransformer extends ParentOutputTransformerAlias
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

        $dto->use_custom_open_graph = $object->getUseCustomOG();
        $dto->og_meta = $object->getOgMeta();

        return $dto;
    }
}
