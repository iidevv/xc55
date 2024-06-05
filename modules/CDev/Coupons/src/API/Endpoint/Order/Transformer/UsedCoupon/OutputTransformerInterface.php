<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\API\Endpoint\Order\Transformer\UsedCoupon;

use CDev\Coupons\API\Endpoint\Order\DTO\UsedCoupon\OrderUsedCouponOutput as OutputDTO;
use CDev\Coupons\Model\UsedCoupon;

interface OutputTransformerInterface
{
    public function transform(UsedCoupon $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
