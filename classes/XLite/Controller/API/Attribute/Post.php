<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\Attribute;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use XLite\Model\Attribute;

final class Post
{
    protected string $type;

    public function __construct(
        string $type
    ) {
        $this->type = $type;
    }

    public function __invoke(Attribute $data): Attribute
    {
        $data->setType($this->type);

        if ($data->getAttributeGroup() && $data->getAttributeGroup()->getProductClass()) {
            throw  new InvalidArgumentException(
                sprintf(
                    'The group (%d) does not match the non-class attribute',
                    $data->getAttributeGroup()->getId()
                )
            );
        }

        return $data;
    }
}
