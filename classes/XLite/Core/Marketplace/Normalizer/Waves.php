<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace\Normalizer;

class Waves extends \XLite\Core\Marketplace\Normalizer
{
    public function normalize($response)
    {
        return $response['waves'];
    }
}
