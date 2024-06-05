<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\API\Endpoint\ProductAttachment\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Exception;
use CDev\FileAttachments\API\Endpoint\ProductAttachment\DTO\ProductAttachmentOutput as OutputDTO;
use CDev\FileAttachments\Model\Product\Attachment as Model;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Model $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->title = $object->getTitle();
        $dto->description = $object->getDescription();
        $dto->access = $object->getAccess();
        $dto->position = $object->getOrderby();
        $dto->url = $object->getURL();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }
}
