<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Model;

class ManageAttributeGroups extends \XLite\View\Pager\Admin\Model\Table
{
    public const MAX_VISIBLE_PAGES = 4;

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    protected function getPagesPerFrame()
    {
        return 1;
    }
}
