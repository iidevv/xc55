<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\API\Endpoint\ProductSticker\Transformer;

use QSL\ProductStickers\API\Endpoint\ProductSticker\DTO\ProductStickerInput as InputDTO;
use QSL\ProductStickers\Model\ProductSticker as Model;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Model;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
