<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AmpRequest extends \XLite\Core\Request
{
    /**
     * Check if request is XHR made with AMP runtime (for example amp-form with action-xhr)
     *
     * @return boolean
     */
    public function isAmpXhr()
    {
        return isset($_SERVER['HTTP_ORIGIN']) && isset($this->__amp_source_origin)
            || isset($_SERVER['HTTP_AMP_SAME_ORIGIN']) && $_SERVER['HTTP_AMP_SAME_ORIGIN'] === 'true';
    }
}
