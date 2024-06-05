<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubEntityInputTransformer;

use Doctrine\Common\Collections\Collection;

interface SubEntityIdCollectionInputTransformerInterface
{
    public function update(Collection $collection, array $idList): void;
}
