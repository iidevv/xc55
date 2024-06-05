<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * License keys notice page controller
 */
class KeysNotice extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Do action 'Re-check'
     *
     * @return void
     */
    protected function doActionRecheck()
    {
        \XLite\Core\Marketplace::getInstance()->clearCache();

        $returnUrl = \XLite\Core\Request::getInstance()->returnUrl ?: $this->buildURL('main');

        $this->setReturnURL($returnUrl);
    }
}
