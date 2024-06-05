<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab\ResourceTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XC\CustomProductTabs\API\Resource\CustomProductTab as CustomProductTabResource;
use XC\CustomProductTabs\Model\Product\CustomGlobalTab;

class InputTransformer implements DataTransformerInterface, InputTransformerInterface
{
    /**
     * @param CustomProductTabResource $object
     */
    public function transform($object, string $to, array $context = []): CustomGlobalTab
    {
        /** @var CustomGlobalTab $model */
        $model = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE];
        $model->setName($object->name);
        $model->setContent($object->content);
        $model->setBriefInfo($object->brief_info);

        return $model;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof CustomGlobalTab) {
            return false;
        }

        return $to === CustomGlobalTab::class;
    }
}
