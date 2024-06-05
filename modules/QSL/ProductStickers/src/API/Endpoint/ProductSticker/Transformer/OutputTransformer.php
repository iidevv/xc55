<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\API\Endpoint\ProductSticker\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Exception;
use QSL\ProductStickers\API\Endpoint\ProductSticker\DTO\ProductStickerOutput as OutputDTO;
use QSL\ProductStickers\Model\ProductSticker as Model;

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
        $dto->id = $object->getProductStickerId();
        $dto->name = $object->getName();
        $dto->position = $object->getPosition();
        $dto->enabled = $object->getEnabled();
        $dto->text_color = $object->getTextColor();
        $dto->bg_color = $object->getBgColor();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }
}
