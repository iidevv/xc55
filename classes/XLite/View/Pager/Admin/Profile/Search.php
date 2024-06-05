<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Profile;

/**
 * Pager for the users search page
 */
class Search extends \XLite\View\Pager\Admin\Profile\AProfile
{
    /**
     * Do not show pager on bottom
     *
     * @return boolean
     */
    protected function isVisibleBottom()
    {
        return false;
    }
}
