<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubEntityOutputTransformer;

use Doctrine\Common\Collections\Collection;

interface SubEntityIdCollectionOutputTransformerInterface
{
    /**
     * @param Collection $entities
     *
     * @return int[]|string[]
     */
    public function transform(Collection $entities): array;
}
