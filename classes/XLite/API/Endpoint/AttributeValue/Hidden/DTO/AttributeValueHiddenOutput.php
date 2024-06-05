<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Hidden\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XLite\API\Endpoint\AttributeOption\Hidden\DTO\AttributeOptionHiddenOutput as OutputOption;

final class AttributeValueHiddenOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @var OutputOption
     * @Assert\NotNull()
     */
    public OutputOption $option;
}
