<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params;

interface SetUrlInterface
{
    public const PARAM_URL = "url";

    /**
     * @return void
     */
    public function setUrl(): void;
}