<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Controller\API\ProductSticker;

use QSL\ProductStickers\Model\ProductSticker as Model;

final class Post
{
    public function __invoke(Model $data): Model
    {
        return $data;
    }
}
