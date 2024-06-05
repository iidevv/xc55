<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory\Commands;

use Qualiteam\SkinActYotpoReviews\Core\Command\Update\AverageRatingAndVotesCount;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Get\Request;

class UpdateAverageRatingAndVotesCountFactory
{
    public function __construct(
        private Request $container
    ) {
    }

    public function createCommand()
    {
        return new AverageRatingAndVotesCount($this->container);
    }
}
