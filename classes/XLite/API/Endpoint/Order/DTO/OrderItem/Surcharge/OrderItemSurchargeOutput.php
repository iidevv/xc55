<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\DTO\OrderItem\Surcharge;

use Symfony\Component\Validator\Constraints as Assert;

class OrderItemSurchargeOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=8)
     * @var string
     */
    public string $type;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=128)
     * @var string
     */
    public string $code;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=255)
     * @var string
     */
    public string $class;

    /**
     * @var bool
     */
    public bool $include = false;

    /**
     * @var bool
     */
    public bool $available = true;

    /**
     * @var float
     */
    public float $value;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=255)
     * @var string
     */
    public string $name;

    /**
     * @var int
     */
    public int $weight = 0;
}
