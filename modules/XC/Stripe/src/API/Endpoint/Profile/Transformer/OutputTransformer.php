<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\API\Endpoint\Profile\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileOutput as OutputDTO;
use XLite\API\Endpoint\Profile\Transformer\OutputTransformer as ExtendedOutputTransformer;
use XC\Stripe\API\Endpoint\Profile\DTO\ProfileOutput as ModuleOutputDTO;
use XC\Stripe\Model\Profile as Model;

/**
 * @Extender\Mixin
 */
class OutputTransformer extends ExtendedOutputTransformer
{
    /**
     * @param Model $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        /** @var ModuleOutputDTO $dto */
        $dto = parent::transform($object, $to, $context);

        $dto->stripe_customer_id = $object->getStripeCustomerId();

        return $dto;
    }
}
