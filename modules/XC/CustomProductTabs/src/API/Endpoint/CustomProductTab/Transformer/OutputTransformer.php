<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XC\CustomProductTabs\API\Endpoint\CustomProductTab\DTO\Output as CustomProductTabOutput;
use XC\CustomProductTabs\API\Resource\CustomProductTab;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param CustomProductTab $object
     */
    public function transform($object, string $to, array $context = []): CustomProductTabOutput
    {
        $output = new CustomProductTabOutput();
        $output->id = $object->id;
        $output->name = $object->name;
        $output->content = $object->content;
        $output->brief_info = $object->brief_info;

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === CustomProductTabOutput::class && $data instanceof CustomProductTab;
    }
}
