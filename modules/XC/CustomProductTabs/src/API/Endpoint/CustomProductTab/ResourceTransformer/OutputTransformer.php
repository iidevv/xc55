<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab\ResourceTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XC\CustomProductTabs\API\Resource\CustomProductTab as CustomProductTabResource;
use XC\CustomProductTabs\Model\Product\CustomGlobalTab;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param CustomGlobalTab $object
     */
    public function transform($object, string $to, array $context = []): CustomProductTabResource
    {
        $resource = new CustomProductTabResource();
        $resource->id = $object->getGlobalTab()->getId();
        $resource->name = $object->getName();
        $resource->content = $object->getContent();
        $resource->brief_info = $object->getBriefInfo();

        return $resource;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === CustomProductTabResource::class && $data instanceof CustomGlobalTab;
    }
}
