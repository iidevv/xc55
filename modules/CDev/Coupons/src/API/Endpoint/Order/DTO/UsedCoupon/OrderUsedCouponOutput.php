<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\API\Endpoint\Order\DTO\UsedCoupon;

use Symfony\Component\Validator\Constraints as Assert;

class OrderUsedCouponOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=16)
     * @var string
     */
    public string $code;

    /**
     * @var float
     */
    public float $value;

    /**
     * @Assert\Length(min=1, max=1)
     * @var string|null
     */
    public ?string $type;

    /**
     * @Assert\Positive()
     * @var int|null
     */
    public ?int $coupon_id;
}
