<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Select\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AttributeValueSelectInput
{
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $option_id = 0;

    /**
     * @var int
     */
    public int $position = 0;

    /**
     * @var float
     */
    public float $price_modifier = 0.0;

    /**
     * @var string
     * @Assert\Choice({"absolute", "percent"})
     */
    public string $price_modifier_type = 'percent';

    /**
     * @var float
     */
    public float $weight_modifier = 0.0;

    /**
     * @var string
     * @Assert\Choice({"absolute", "percent"})
     */
    public string $weight_modifier_type = 'percent';

    /**
     * @var bool
     */
    public bool $is_default = false;
}
