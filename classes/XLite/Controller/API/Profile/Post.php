<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\Profile;

use XLite\Model\Profile;

final class Post
{
    public function __invoke(Profile $data): Profile
    {
        return $data;
    }
}
