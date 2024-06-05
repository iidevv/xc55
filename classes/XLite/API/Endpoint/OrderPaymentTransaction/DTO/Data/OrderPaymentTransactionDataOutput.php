<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\DTO\Data;

use Symfony\Component\Validator\Constraints as Assert;

class OrderPaymentTransactionDataOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\Length(min=1, max=128)
     * @var string
     */
    public string $name;

    /**
     * @Assert\Length(min=1, max=255)
     * @var string
     */
    public string $label;

    /**
     * @var string
     */
    public string $value;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=1)
     * @var string
     */
    public string $access_level;
}
