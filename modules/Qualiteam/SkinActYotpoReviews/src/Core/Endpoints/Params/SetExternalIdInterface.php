<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params;

interface SetExternalIdInterface
{
    public const PARAM_EXTERNAL_ID = "external_id";

    /**
     * @return void
     */
    public function setExternalId(): void;
}