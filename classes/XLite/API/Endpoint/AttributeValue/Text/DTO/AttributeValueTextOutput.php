<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Text\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AttributeValueTextOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $value;

    /**
     * @var bool
     */
    public bool $editable;
}
