<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubEntityOutputTransformer;

interface SubEntityIdOutputTransformerInterface
{
    /**
     * @param object|null $entity
     *
     * @return int|string|null
     */
    public function transform(?object $entity);
}
