<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints;

use XLite\Model\AEntity;

interface EndpointInterface
{
    /**
     * @param \XLite\Model\AEntity|null $entity
     *
     * @return array
     */
    public function getData(?AEntity $entity): array;
}
