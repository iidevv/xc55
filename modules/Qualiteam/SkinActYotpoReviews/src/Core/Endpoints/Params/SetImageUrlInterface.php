<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params;

interface SetImageUrlInterface
{
    public const PARAM_IMAGE_URL = "image_url";

    /**
     * @return void
     */
    public function setImageUrl(): void;
}