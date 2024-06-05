<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Account;

/**
 * Delete account confirmation widget
 */
class Delete extends \XLite\View\AView
{
    /**
     * getDefaultTemplate
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'account/confirm_delete.twig';
    }
}
