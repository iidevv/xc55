<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Hidden\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class AttributeValueHiddenInput
{
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $option_id = 0;
}
