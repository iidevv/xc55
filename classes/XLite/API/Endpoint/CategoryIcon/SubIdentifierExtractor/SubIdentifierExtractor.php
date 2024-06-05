<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryIcon\SubIdentifierExtractor;

use XCart\Framework\ApiPlatform\Core\Api\SubIdentifierExtractor\SubIdentifierExtractorInterface;
use XLite\Model\Image\Category\Image;

class SubIdentifierExtractor implements SubIdentifierExtractorInterface
{
    public function getIdentifiersFromResourceClass(string $resourceClass): array
    {
        return [
            'category_id',
        ];
    }

    /**
     * @var Image $item
     */
    public function getIdentifiersFromItem($item): array
    {
        return [
            'id' => $item->getId(),
        ];
    }

    public function supportResourceClass(string $resourceClass): bool
    {
        return $resourceClass === Image::class;
    }

    public function supportItem(object $item): bool
    {
        return $this->supportResourceClass(get_class($item));
    }
}
