<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace\Normalizer;

class InstallationData extends \XLite\Core\Marketplace\Normalizer
{
    /**
     * @param array $response
     *
     * @return array
     */
    public function normalize($response)
    {
        return $response['installationData'] ?? [];
    }
}
