<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Get;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\ConstructorInterface;

class RequestConstructor implements ConstructorInterface
{
    public function build(): void
    {}

    /**
     * @return array
     */
    public function getBody(): array
    {
        return [];
    }
}