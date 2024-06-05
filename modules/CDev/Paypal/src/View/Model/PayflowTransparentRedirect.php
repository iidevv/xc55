<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Model;

/**
 * PayflowTransparentRedirect
 */
class PayflowTransparentRedirect extends \CDev\Paypal\View\Model\ASettings
{
    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        unset($this->schemaAdditional['buyNowEnabled']);
    }
}
