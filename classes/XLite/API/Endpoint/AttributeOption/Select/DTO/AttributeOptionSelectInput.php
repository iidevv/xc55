<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeOption\Select\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AttributeOptionSelectInput
{
    /**
     * @var int
     */
    public int $position = 0;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     * @var string
     */
    public string $value = '';
}
