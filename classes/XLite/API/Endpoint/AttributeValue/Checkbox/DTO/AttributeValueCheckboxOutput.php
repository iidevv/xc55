<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Checkbox\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AttributeValueCheckboxOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @var bool
     */
    public bool $value;

    /**
     * @var float
     */
    public float $price_modifier;

    /**
     * @var string
     * @Assert\Choice({"absolute", "percent"})
     */
    public string $price_modifier_type = 'percent';

    /**
     * @var float
     */
    public float $weight_modifier;

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
