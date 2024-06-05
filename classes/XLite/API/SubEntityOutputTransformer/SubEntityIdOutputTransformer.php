<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubEntityOutputTransformer;

use XLite\API\Helper\IdGetterInterface;

class SubEntityIdOutputTransformer implements SubEntityIdOutputTransformerInterface
{
    protected IdGetterInterface $idGetter;

    public function __construct(IdGetterInterface $idGetter)
    {
        $this->idGetter = $idGetter;
    }

    /**
     * @param object|null $entity
     *
     * @return int|string|null
     */
    public function transform(?object $entity)
    {
        return $entity ? $this->idGetter->getId($entity) : null;
    }
}
