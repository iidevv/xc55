<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory\Commands;

use Qualiteam\SkinActYotpoReviews\Core\Api\Reviews\CollectAllBuilder;
use Qualiteam\SkinActYotpoReviews\Core\Command\Update\Reviews;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Reviews\Get\CollectAll;

class UpdateYotpoReviewsCommandFactory
{
    public function __construct(
        private CollectAll $container,
        private CollectAllBuilder $collectAllBuilder
    ) {
    }

    public function createCommand(): Reviews
    {
        return new Reviews($this->container, $this->collectAllBuilder);
    }
}