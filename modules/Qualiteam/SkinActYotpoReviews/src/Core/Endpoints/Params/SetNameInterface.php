<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params;

interface SetNameInterface
{
    public const PARAM_NAME = "name";

    /**
     * @return void
     */
    public function setName(): void;
}