<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubEntityOutputTransformer;

use Doctrine\Common\Collections\Collection;
use XLite\API\Helper\IdGetterInterface;

class SubEntityIdCollectionOutputTransformer implements SubEntityIdCollectionOutputTransformerInterface
{
    protected IdGetterInterface $idGetter;

    public function __construct(IdGetterInterface $idGetter)
    {
        $this->idGetter = $idGetter;
    }

    /**
     * @param Collection $entities
     *
     * @return int[]|string[]
     */
    public function transform(Collection $entities): array
    {
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $this->idGetter->getId($entity);
        }

        return $result;
    }
}
