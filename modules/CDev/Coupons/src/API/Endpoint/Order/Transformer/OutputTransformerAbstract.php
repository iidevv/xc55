<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\API\Endpoint\Order\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput;
use CDev\Coupons\API\Endpoint\Order\DTO\Output as ModuleOutputDTO;
use CDev\Coupons\API\Endpoint\Order\Transformer\UsedCoupon\OutputTransformerInterface;

/**
 * @Extender\Mixin
 */
class OutputTransformerAbstract extends \XLite\API\Endpoint\Order\Transformer\OutputTransformerAbstract
{
    protected OutputTransformerInterface $usedCouponTransformer;

    /**
     * @required
     */
    public function setUsedCouponTransformer(OutputTransformerInterface $usedCouponTransformer): void
    {
        $this->usedCouponTransformer = $usedCouponTransformer;
    }

    protected function basicTransform(BaseOutput $dto, $object, string $to, array $context = []): BaseOutput
    {
        /** @var ModuleOutputDTO $dto */
        $dto = parent::basicTransform($dto, $object, $to, $context);

        $dto->used_coupons = [];
        foreach ($object->getUsedCoupons() as $usedCoupon) {
            $dto->used_coupons[] = $this->usedCouponTransformer->transform($usedCoupon, $to, $context);
        }

        return $dto;
    }
}
