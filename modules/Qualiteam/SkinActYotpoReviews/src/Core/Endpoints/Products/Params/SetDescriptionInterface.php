<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Params;

interface SetDescriptionInterface
{
    public const PARAM_DESCRIPTION = "description";

    /**
     * @return void
     */
    public function setDescription(): void;
}